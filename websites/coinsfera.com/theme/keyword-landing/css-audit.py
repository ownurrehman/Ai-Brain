#!/usr/bin/env python3
"""Audit the design stylesheets for scoping and hygiene.

The landing designs load on a live site alongside the theme's own CSS, so an
unscoped selector would restyle the rest of coinsfera.com. This checks that
every rule is scoped, and flags the habits the design contract rules out.

Usage: python3 css-audit.py [build/assets/css]
"""

import re
import sys
from pathlib import Path

# A selector is acceptable if it is scoped to the landing page in one of these
# ways, or if it is a custom-property-only :root style block on .cfkl.
SCOPE_PATTERNS = (".cfkl", "html.cfkl-js")

AT_RULE = re.compile(r"^\s*@")
COMMENT = re.compile(r"/\*.*?\*/", re.S)


def selectors(css: str):
    """Yield (selector, line_number) for every style rule in the sheet."""
    css_nc = COMMENT.sub(lambda m: "\n" * m.group(0).count("\n"), css)

    depth = 0
    buf = []
    line = 1
    start_line = 1

    for ch in css_nc:
        if ch == "\n":
            line += 1

        if ch == "{":
            if depth == 0:
                prelude = "".join(buf).strip()
                yield prelude, start_line
            depth += 1
            buf = []
            start_line = line
        elif ch == "}":
            depth = max(0, depth - 1)
            buf = []
            start_line = line
        else:
            if not buf:
                start_line = line
            buf.append(ch)

            # Track nested at-rule bodies: selectors inside @media are depth 1.
            if depth == 1 and ch == ";":
                buf = []


def split_selectors(prelude: str):
    """Split a selector list on commas that are not inside brackets.

    Needed because the base sheet leans on :where(h1, h2, h3) to keep its
    reset at zero specificity, and a naive split would read h2 as a separate,
    unscoped selector.
    """
    parts = []
    depth = 0
    buf = []

    for ch in prelude:
        if ch in "([":
            depth += 1
        elif ch in ")]":
            depth = max(0, depth - 1)

        if ch == "," and depth == 0:
            parts.append("".join(buf))
            buf = []
        else:
            buf.append(ch)

    parts.append("".join(buf))

    return [p.strip() for p in parts if p.strip()]


def reduced_motion_spans(css: str):
    """Character ranges of prefers-reduced-motion blocks.

    Overriding animation and transition inside one is the accepted way to
    honour the preference against arbitrary later rules, so !important is
    allowed there and nowhere else.
    """
    spans = []

    for match in re.finditer(r"@media[^{]*prefers-reduced-motion[^{]*\{", css):
        start = match.end() - 1
        depth = 0

        for i in range(start, len(css)):
            if css[i] == "{":
                depth += 1
            elif css[i] == "}":
                depth -= 1

                if depth == 0:
                    spans.append((match.start(), i))
                    break

    return spans


def audit(path: Path) -> list:
    raw = path.read_text(encoding="utf-8")

    # Blank out comments while preserving line numbers.
    css = COMMENT.sub(lambda m: "\n" * m.group(0).count("\n"), raw)

    problems = []
    allowed = reduced_motion_spans(css)

    for match in re.finditer(r"!important", css):
        if any(start <= match.start() <= end for start, end in allowed):
            continue

        line = css[: match.start()].count("\n") + 1
        problems.append(f"line {line}: !important")

    for match in re.finditer(r"@import", css):
        line = css[: match.start()].count("\n") + 1
        problems.append(f"line {line}: @import")

    for match in re.finditer(r"url\(\s*['\"]?https?://", css):
        line = css[: match.start()].count("\n") + 1
        problems.append(f"line {line}: external url()")

    nested = []

    for prelude, line in selectors(css):
        if not prelude or AT_RULE.match(prelude):
            # @media / @supports / @keyframes preludes; their inner selectors
            # are checked separately below.
            nested.append((prelude, line))
            continue

        for sel in split_selectors(prelude):
            if not any(p in sel for p in SCOPE_PATTERNS):
                problems.append(f"line {line}: unscoped selector  {sel[:70]}")

    # Selectors nested inside at-rules.
    for block in re.finditer(r"@(?:media|supports)[^{]*\{(.*?)\n\}", css, re.S):
        body = block.group(1)
        offset = css[: block.start()].count("\n") + 1

        for prelude, line in selectors(body):
            if not prelude or AT_RULE.match(prelude):
                continue

            for sel in split_selectors(prelude):
                if any(p in sel for p in SCOPE_PATTERNS):
                    continue

                # Percentage keyframe steps are not selectors.
                if re.fullmatch(r"(from|to|[\d.]+%)", sel):
                    continue

                problems.append(f"~line {offset + line}: unscoped in at-rule  {sel[:60]}")

    return problems


def main():
    base = Path(sys.argv[1] if len(sys.argv) > 1 else "build/assets/css")
    sheets = sorted(base.glob("design-*.css")) + sorted(base.glob("keyword-landing.css"))

    if not sheets:
        print(f"no stylesheets found in {base}")
        return 1

    failed = False

    for sheet in sheets:
        problems = audit(sheet)
        size = sheet.stat().st_size
        lines = sheet.read_text(encoding="utf-8").count("\n")

        status = "clean" if not problems else f"{len(problems)} problem(s)"
        print(f"\n{sheet.name}  ({lines} lines, {size:,} bytes)  {status}")

        for problem in problems[:25]:
            print(f"    {problem}")

        if len(problems) > 25:
            print(f"    ... and {len(problems) - 25} more")

        if problems:
            failed = True

    print()
    return 1 if failed else 0


if __name__ == "__main__":
    sys.exit(main())
