#!/usr/bin/env python3
"""
Fast YouTube transcript fetcher — HTTP fallback via youtube-transcript-api.
Falls back to yt-dlp if no subtitles or transcript unavailable.
"""
import argparse
import sys
import re
from urllib.parse import urlparse, parse_qs

try:
    from youtube_transcript_api import YouTubeTranscriptApi
    YT_API_AVAILABLE = True
except ImportError:
    YT_API_AVAILABLE = False

def extract_video_id(url: str) -> str:
    """Extract YouTube video ID from any URL format."""
    if len(url) == 11 and re.match(r'^[A-Za-z0-9_-]{11}$', url):
        return url
    parsed = urlparse(url)
    if parsed.hostname in ('youtube.com', 'www.youtube.com', 'm.youtube.com'):
        qs = parse_qs(parsed.query)
        if 'v' in qs:
            return qs['v'][0]
        m = re.search(r'/embed/([A-Za-z0-9_-]{11})', parsed.path)
        if m:
            return m.group(1)
    if parsed.hostname in ('youtu.be', 'www.youtu.be'):
        path = parsed.path.lstrip('/')
        if re.match(r'^[A-Za-z0-9_-]{11}$', path):
            return path
    raise ValueError(f"Cannot extract video ID from: {url}")

def fetch_fast(video_id: str, languages=None):
    """Fast HTTP method: ~0.5s per video."""
    if languages is None:
        languages = ['en', 'en-US', 'en-GB']
    try:
        transcript = YouTubeTranscriptApi.get_transcript(video_id, languages=languages)
        lines = [segment['text'] for segment in transcript]
        return '\n'.join(lines)
    except Exception:
        return None

def fetch_fallback(video_id: str):
    """Fallback: yt-dlp download method."""
    import subprocess
    import tempfile
    from pathlib import Path
    
    url = f"https://www.youtube.com/watch?v={video_id}"
    with tempfile.TemporaryDirectory() as temp_dir:
        cmd = [
            "yt-dlp",
            "--write-subs",
            "--write-auto-subs",
            "--skip-download",
            "--sub-lang", "en",
            "--output", "subs",
            url
        ]
        try:
            subprocess.run(cmd, cwd=temp_dir, check=True, capture_output=True)
        except (subprocess.CalledProcessError, FileNotFoundError):
            return None
        
        temp_path = Path(temp_dir)
        vtt_files = list(temp_path.glob("*.vtt"))
        if not vtt_files:
            return None
        
        content = vtt_files[0].read_text(encoding='utf-8')
        return clean_vtt(content)

def clean_vtt(content: str) -> str:
    """Clean WebVTT content to plain text."""
    lines = content.splitlines()
    text_lines = []
    timestamp_pattern = re.compile(r'\d{2}:\d{2}:\d{2}\.\d{3}\s--\u003e\s\d{2}:\d{2}:\d{2}\.\d{3}')
    
    for line in lines:
        line = line.strip()
        if not line or line == 'WEBVTT' or line.isdigit():
            continue
        if timestamp_pattern.match(line):
            continue
        if line.startswith('NOTE') or line.startswith('STYLE'):
            continue
        if text_lines and text_lines[-1] == line:
            continue
        line = re.sub(r'<[^\u003e]+>', '', line)
        text_lines.append(line)
    
    return '\n'.join(text_lines)

def main():
    parser = argparse.ArgumentParser(description="Fast YouTube transcript fetcher")
    parser.add_argument("url", help="YouTube video URL or video ID")
    parser.add_argument("--fallback", action="store_true", help="Force yt-dlp fallback")
    args = parser.parse_args()
    
    try:
        video_id = extract_video_id(args.url)
    except ValueError as e:
        print(e, file=sys.stderr)
        sys.exit(1)
    
    # Try fast method first
    text = None
    if not args.fallback and YT_API_AVAILABLE:
        text = fetch_fast(video_id)
    
    # Fallback to yt-dlp if needed
    if text is None:
        text = fetch_fallback(video_id)
    
    if text is None:
        print("No transcript available for this video.", file=sys.stderr)
        sys.exit(1)
    
    print(text)

if __name__ == "__main__":
    main()
