#!/usr/bin/env python3
"""
Revert media uploads: Unlink featured images from posts and delete media.
Uses hardcoded list from the last session's output.
"""
import requests
import json
import sys
import time

WP_URL = "https://rankray.com"
USERNAME = "openclaw"
APP_PASSWORD = "6Zz9 5gJL 8uyA QH4g RQDH GV1j"

auth = requests.auth.HTTPBasicAuth(USERNAME, APP_PASSWORD)
headers = {
    "Content-Type": "application/json",
    "User-Agent": "OpenClaw/1.0"
}

# All 76 draft posts with their featured_media IDs from the session
POSTS_DATA = [
    (20542, 20877), (20492, 20876), (20528, 20875), (20537, 20874), (20480, 20873),
    (20467, 20872), (20507, 20870), (20508, 20869), (20540, 20868), (20525, 20867),
    (20514, 20866), (20479, 20865), (20536, 20864), (20476, 20863), (20503, 20862),
    (20466, 20861), (20533, 20860), (20474, 20859), (20473, 20858), (20522, 20857),
    (20481, 20856), (20539, 20855), (20517, 20854), (20506, 20852), (20490, 20851),
    (20511, 20850), (20499, 20849), (20502, 20848), (20520, 20847), (20524, 20846),
    (20470, 20845), (20512, 20844), (20469, 20843), (20468, 20842), (20515, 20841),
    (20489, 20840), (20482, 20839), (20541, 20838), (20494, 20837), (20478, 20836),
    (20532, 20835), (20498, 20834), (20475, 20833), (20526, 20832), (20493, 20831),
    (20463, 20830), (20527, 20829), (20501, 20828), (20523, 20827), (20513, 20826),
    (20534, 20825), (20538, 20824), (20496, 20823), (20465, 20822), (20497, 20821),
    (20543, 20820), (20471, 20819), (20509, 20818), (20530, 20817), (20477, 20816),
    (20510, 20815), (20505, 20814), (20491, 20813), (20516, 20812), (20518, 20811),
    (20535, 20810), (20504, 20809), (20500, 20808), (20519, 20807), (20472, 20806),
    (20521, 20805), (20531, 20804), (20464, 20803), (20495, 20802), (20529, 20801),
    (20414, 20415)
]


def unlink_featured_media(post_id):
    """Set featured_media to 0 on a post."""
    url = f"{WP_URL}/wp-json/wp/v2/posts/{post_id}"
    data = {"featured_media": 0}
    resp = requests.post(url, auth=auth, headers=headers, json=data, timeout=60)
    return resp.status_code == 200, resp.status_code, resp.text[:200]


def delete_media(media_id):
    """Permanently delete a media item."""
    url = f"{WP_URL}/wp-json/wp/v2/media/{media_id}?force=true"
    resp = requests.delete(url, auth=auth, headers=headers, timeout=60)
    return resp.status_code in [200, 202, 410], resp.status_code, resp.text[:200]


def verify_post_unlinked(post_id):
    """Verify post has featured_media = 0."""
    url = f"{WP_URL}/wp-json/wp/v2/posts/{post_id}"
    resp = requests.get(url, auth=auth, headers=headers, timeout=60)
    if resp.status_code == 200:
        return resp.json().get("featured_media", -1) == 0
    return False


def main():
    print("=== STEP 1: Confirming data ===")
    print(f"Total posts to process: {len(POSTS_DATA)}")
    
    # Collect unique media IDs
    media_ids = set()
    for post_id, media_id in POSTS_DATA:
        media_ids.add(media_id)
    print(f"Unique media IDs to delete: {len(media_ids)}")
    
    print(f"\n=== STEP 2: Unlink featured_media from {len(POSTS_DATA)} posts ===")
    unlinked = 0
    failed_unlink = []
    for post_id, media_id in POSTS_DATA:
        ok, code, text = unlink_featured_media(post_id)
        if ok:
            unlinked += 1
        else:
            failed_unlink.append({"post_id": post_id, "code": code, "text": text})
            print(f"  FAILED unlink post {post_id}: HTTP {code}")
        if unlinked % 10 == 0:
            print(f"  Unlinked {unlinked}/{len(POSTS_DATA)}...")
        time.sleep(0.3)  # Rate limiting
    print(f"Unlinked {unlinked}/{len(POSTS_DATA)} posts")
    if failed_unlink:
        print(f"  Failed to unlink {len(failed_unlink)} posts")
    
    print(f"\n=== STEP 3: Delete {len(media_ids)} media items ===")
    deleted = 0
    failed_delete = []
    for mid in sorted(media_ids):
        ok, code, text = delete_media(mid)
        if ok:
            deleted += 1
        else:
            failed_delete.append({"media_id": mid, "code": code, "text": text})
            print(f"  FAILED delete media {mid}: HTTP {code}")
        if deleted % 10 == 0:
            print(f"  Deleted {deleted}/{len(media_ids)}...")
        time.sleep(0.3)  # Rate limiting
    print(f"Deleted {deleted}/{len(media_ids)} media items")
    if failed_delete:
        print(f"  Failed to delete {len(failed_delete)} media items")
    
    print(f"\n=== STEP 4: Verify posts are image-less ===")
    verified = 0
    failed_verify = []
    for post_id, _ in POSTS_DATA:
        if verify_post_unlinked(post_id):
            verified += 1
        else:
            failed_verify.append(post_id)
        if verified % 10 == 0:
            print(f"  Verified {verified}/{len(POSTS_DATA)}...")
        time.sleep(0.2)
    print(f"Verified {verified}/{len(POSTS_DATA)} posts have no featured media")
    if failed_verify:
        print(f"  Still have featured media: {failed_verify}")
    
    print(f"\n=== SUMMARY ===")
    print(f"Posts unlinked: {unlinked}/{len(POSTS_DATA)}")
    print(f"Media deleted: {deleted}/{len(media_ids)}")
    print(f"Posts verified image-less: {verified}/{len(POSTS_DATA)}")
    
    # Save detailed report
    report = {
        "posts_count": len(POSTS_DATA),
        "media_count": len(media_ids),
        "posts_unlinked": unlinked,
        "media_deleted": deleted,
        "posts_verified": verified,
        "failed_unlink": failed_unlink,
        "failed_delete": failed_delete,
        "failed_verify": failed_verify
    }
    with open("/tmp/revert_media_report.json", "w") as f:
        json.dump(report, f, indent=2)
    print(f"Report saved to: /tmp/revert_media_report.json")


if __name__ == "__main__":
    main()
