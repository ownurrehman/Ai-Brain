#!/usr/bin/env bash
#
# backup-theme.sh — Justccell theme version snapshot + rotation.
#
# Freezes the current working copy of justccell-theme/ into
# archive/theme-releases/<VERSION>/ and keeps ONLY the newest 10 versions.
#
# archive/theme-releases/ is gitignored (see repo .gitignore) — these are
# local, tangible restore points. The lossless primary backup is git history
# (every committed theme state) — see docs/backup-restore.md.
#
# Usage:
#   scripts/backup-theme.sh            # snapshot current JUSTCCELL_VERSION
#   scripts/backup-theme.sh 0.9.296    # snapshot under an explicit version
#
# Run this after every version bump / deploy.

set -euo pipefail

# Resolve paths relative to this script (scripts/ lives beside justccell-theme/).
SITE_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
THEME_DIR="$SITE_DIR/justccell-theme"
RELEASES_DIR="$SITE_DIR/archive/theme-releases"
KEEP=10

if [[ ! -d "$THEME_DIR" ]]; then
  echo "ERROR: theme dir not found: $THEME_DIR" >&2
  exit 1
fi

# Version: arg 1, else parse JUSTCCELL_VERSION from functions.php.
VERSION="${1:-}"
if [[ -z "$VERSION" ]]; then
  VERSION="$(grep -oE "JUSTCCELL_VERSION', '[0-9.]+'" "$THEME_DIR/functions.php" | grep -oE '[0-9]+\.[0-9]+\.[0-9]+' | head -1 || true)"
fi
if [[ -z "$VERSION" ]]; then
  echo "ERROR: could not determine version (pass it explicitly)." >&2
  exit 1
fi

DEST="$RELEASES_DIR/$VERSION"
mkdir -p "$RELEASES_DIR"

echo "==> Snapshotting theme $VERSION"
echo "    from: $THEME_DIR"
echo "    to:   $DEST"

# Full snapshot (code + assets) so a restore is complete and foolproof.
rsync -a --delete \
  --exclude '.DS_Store' \
  "$THEME_DIR/" "$DEST/"

echo "==> Snapshot done."

# Rotation: keep only the newest $KEEP version folders (by semantic version sort).
# Portable (works on macOS bash 3.2 — no mapfile).
list_versions() {
  find "$RELEASES_DIR" -mindepth 1 -maxdepth 1 -type d -exec basename {} \; \
    | grep -E '^[0-9]+\.[0-9]+\.[0-9]+$' | sort -t. -k1,1n -k2,2n -k3,3n
}

COUNT="$(list_versions | wc -l | tr -d ' ')"
if [[ "$COUNT" -gt "$KEEP" ]]; then
  PRUNE=$(( COUNT - KEEP ))
  echo "==> $COUNT snapshots present; pruning oldest $PRUNE (keep newest $KEEP)."
  list_versions | head -n "$PRUNE" | while read -r v; do
    echo "    removing $RELEASES_DIR/$v"
    rm -rf "${RELEASES_DIR:?}/$v"
  done
else
  echo "==> $COUNT snapshot(s) present (limit $KEEP) — no pruning needed."
fi

echo "==> Current restore points:"
list_versions | sed 's/^/    /'
