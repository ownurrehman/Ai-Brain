#!/usr/bin/env python3
"""
cold_email_check.py — Pre-flight gate for cold outreach drafts.

Reads a draft file (.txt, .md, or .json with `subject` and `body_text`
fields) and checks it against the marketing-copywriting playbook's
anti-AI checklist. Returns pass/fail with a per-line breakdown of
violations.

Usage:
    python3 cold_email_check.py /path/to/draft.txt
    python3 cold_email_check.py /path/to/drafts/  # all drafts in dir
    cat draft.txt | python3 cold_email_check.py -

Exit code 0 = pass, 1 = fail. Designed to be run before sending OR
before showing drafts to the user.
"""

import sys, os, json, re
from pathlib import Path

# Banned phrases — checked case-insensitively against subject + body
BANNED_OPENERS = [
    "hi there",
    "i hope this email finds you well",
    "i hope you are doing well",
    "my name is",
    "great to e-meet you",
    "great to connect",
]

BANNED_SIGNOFFS = [
    "best,\n",
    "kind regards,\n",
    "warm regards,\n",
    "sincerely,\n",
    "best, rank ray team",
    "best regards,",
]

BANNED_CTAS = [
    "worth a 10-minute call",
    "book a free audit",
    "book a free consultation",
    "schedule a call",
    "schedule a consultation",
    "hop on a call",
    "set up a time to chat",
]

BANNED_BUZZWORDS = [
    "leverage",
    "robust",
    "seamless",
    "cutting-edge",
    "transformative",
    "elevate",
    "unlock",
    "navigate the landscape",
    "in today's world",
    "dive deep",
    "delve",
    "harness",
    "empower",
]

BANNED_REASSURANCE = [
    "hope this helps",
    "let me know if you have questions",
    "let me know if you need",
    "feel free to reach out",
    "don't hesitate",
]

# Patterns that indicate AI slop even if individual words pass
NEGATIVE_PARALLELISM = re.compile(r"\bit'?s not (just|merely|only) .+; it'?s\b", re.I)
FAKE_CASE_STUDY = re.compile(
    r"\b(took|got|helped).+from.+to.+in \d+ days?\b",
    re.I,
)
RULE_OF_THREE = re.compile(
    r"\b\w+,\s+\w+(?:,\s+or)?\s+\w+,?\s+and\s+\w+\b",
    re.I,
)
EM_DASH = "—"
HTML_TAG = re.compile(r"<\s*[a-zA-Z][^>]*>")


def extract_subject_body(text: str) -> tuple[str, str]:
    """Pull subject from a draft file. Supports 3 formats:
    - .txt with 'SUBJECT:' header line and rest as body
    - .md with '# subject' first heading and rest as body
    - JSON with `subject` and `body_text` keys
    """
    stripped = text.strip()

    # Try JSON first
    if stripped.startswith("{"):
        try:
            d = json.loads(stripped)
            return d.get("subject", ""), d.get("body_text", "")
        except json.JSONDecodeError:
            pass

    # .txt with SUBJECT: header
    if stripped.upper().startswith("SUBJECT:"):
        lines = stripped.split("\n", 1)
        subj = lines[0].split(":", 1)[1].strip()
        body = lines[1] if len(lines) > 1 else ""
        return subj, body

    # .md with first heading as subject
    lines = stripped.split("\n")
    subj = ""
    for ln in lines:
        if ln.startswith("#"):
            subj = ln.lstrip("#").strip()
            break
    body = stripped
    return subj, body


def check_draft(text: str, source: str = "<input>") -> tuple[bool, list[str]]:
    """Returns (passed, list_of_violations)."""
    violations = []
    subject, body = extract_subject_body(text)
    full = f"{subject}\n{body}".lower()

    # ----- SUBJECT RULES -----
    if not subject:
        violations.append("SUBJECT: empty")
    else:
        if EM_DASH in subject:
            violations.append(f"SUBJECT contains em-dash: {subject!r}")
        if re.search(r"[A-Z]{4,}", subject):
            violations.append(f"SUBJECT has ALL-CAPS burst: {subject!r}")
        if any(c in subject for c in "🚨⚡🔥📈💡✅"):
            violations.append(f"SUBJECT has emoji: {subject!r}")
        # word count
        subj_words = subject.split()
        if len(subj_words) > 6:
            violations.append(
                f"SUBJECT too long ({len(subj_words)} words): {subject!r}  "
                f"(target 2-4)"
            )

    # ----- BODY RULES -----
    if not body.strip():
        violations.append("BODY: empty")
    else:
        if HTML_TAG.search(body):
            violations.append("BODY contains HTML tags (plain-text-only rule violated)")

        if EM_DASH in body:
            count = body.count(EM_DASH)
            violations.append(f"BODY contains {count} em-dash(es) — replace with commas/periods")

        # opener check (first non-empty line)
        first_lines = [ln.strip() for ln in body.split("\n") if ln.strip()]
        if first_lines:
            opener = first_lines[0].lower()
            for banned in BANNED_OPENERS:
                if opener.startswith(banned) or banned in opener[:60]:
                    violations.append(f"BODY opener uses banned phrase: {first_lines[0]!r}")

        # sign-off check (last 5 non-empty lines)
        if len(first_lines) >= 3:
            tail = "\n".join(first_lines[-5:]).lower()
            for banned in BANNED_SIGNOFFS:
                if banned.strip() in tail:
                    violations.append(f"BODY sign-off uses banned phrase: {banned.strip()!r}")
            # also flag full name + title + phone + calendar (footer template)
            if re.search(r"\bfounder\b", tail) and re.search(r"\b\d{3,}\b", tail):
                violations.append("BODY sign-off has full title + phone (footer-template smell)")

        # CTA check — look for any banned CTA in last 6 lines
        if len(first_lines) >= 3:
            cta_zone = "\n".join(first_lines[-6:]).lower()
            for banned in BANNED_CTAS:
                if banned in cta_zone:
                    violations.append(f"CTA asks for calendar time on first touch: {banned!r}")

        # buzzwords
        for word in BANNED_BUZZWORDS:
            if re.search(rf"\b{re.escape(word)}\b", full):
                violations.append(f"BUZZWORD found: {word!r}")

        # reassurance kickers
        for phrase in BANNED_REASSURANCE:
            if phrase in full:
                violations.append(f"REASSURANCE kicker found: {phrase!r}")

        # negative parallelism
        if NEGATIVE_PARALLELISM.search(body):
            violations.append("NEGATIVE PARALLELISM ('it's not just X; it's Y') detected")

        # fake case study
        if FAKE_CASE_STUDY.search(body):
            violations.append(
                "FAKE CASE STUDY pattern ('from X to Y in N days') detected — only include defensible specifics"
            )

        # rule-of-three (heuristic, can false-positive — flag, don't fail hard)
        three_matches = RULE_OF_THREE.findall(body)
        if three_matches:
            violations.append(
                f"RULE-OF-THREE pattern ({len(three_matches)}x): rewrite to vary rhythm "
                f"— e.g. {three_matches[0]!r}"
            )

        # word count
        words = body.split()
        if len(words) > 150:
            violations.append(f"BODY too long: {len(words)} words (cap 150, target 80-110)")
        elif len(words) < 50:
            violations.append(f"BODY suspiciously short: {len(words)} words (target 80-110)")

    return (len(violations) == 0, violations)


def main():
    if len(sys.argv) < 2:
        print("Usage: cold_email_check.py <file_or_dir>")
        sys.exit(2)

    target = sys.argv[1]

    if target == "-":
        # stdin
        text = sys.stdin.read()
        passed, violations = check_draft(text, "<stdin>")
        _report(target, passed, violations)
        sys.exit(0 if passed else 1)

    path = Path(target)
    if path.is_file():
        text = path.read_text(encoding="utf-8")
        passed, violations = check_draft(text, str(path))
        _report(str(path), passed, violations)
        sys.exit(0 if passed else 1)

    if path.is_dir():
        any_fail = False
        files = sorted(path.glob("*.txt")) + sorted(path.glob("*.md")) + sorted(path.glob("*.json"))
        if not files:
            print(f"No draft files found in {path}")
            sys.exit(2)
        for f in files:
            text = f.read_text(encoding="utf-8")
            passed, violations = check_draft(text, str(f))
            _report(str(f), passed, violations)
            if not passed:
                any_fail = True
        sys.exit(1 if any_fail else 0)

    print(f"Path not found: {path}")
    sys.exit(2)


def _report(source: str, passed: bool, violations: list[str]):
    icon = "✓ PASS" if passed else "✗ FAIL"
    print(f"\n{icon}  {source}")
    if not violations:
        print("  (no violations)")
    else:
        for v in violations:
            print(f"  - {v}")


if __name__ == "__main__":
    main()
