Summary: `ai-brain` is a compact operating layer for Codex and similar AI coding assistants.

# AI Brain

## What it is

`ai-brain` is a small, reusable knowledge and skills system. It stores durable rules, routing guidance, and narrowly scoped skills that an assistant can load selectively.

## Why it exists

- Reduce prompt waste.
- Improve instruction discovery.
- Keep reusable knowledge separate from one-off chat history.
- Make maintenance easy for both humans and AI.

## Why it is intentionally compact

This folder is designed to stay smaller than the problems it helps solve. If a file does not reduce ambiguity, improve reuse, or lower prompt cost, it should not exist.

## How humans should use it

- Start with [`INDEX.md`](INDEX.md).
- Edit files that contain durable guidance only.
- Prefer updating an existing file over creating a new one.
- Add skills only for recurring jobs with clear trigger conditions.

## How AI should use it

- Read [`INDEX.md`](INDEX.md) first.
- Load only the files needed for the current task.
- Prefer a matching skill before broad context files.
- Skip anything that does not change the decision or output.

## Why it is not a wiki

This is not a full knowledge base, note archive, or prose mirror of the business. It is a routing layer plus a small set of reusable operating instructions. Long explanations, diary notes, and duplicated documentation do not belong here.
