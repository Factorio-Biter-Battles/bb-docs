# Contributing to BB Docs

This repository is the text of biterbattles.org. It is open because the people who read it
are the people who know when it is wrong.

## The one rule that never changes

**Every pull request is reviewed by a human before it goes live. There is no auto-merge,
there will never be one, and no bot has merge rights.** The website pulls the `main` branch
every five minutes, so a merge *is* a deployment — which is exactly why a person looks at it
first.

## What gets merged, and how fast

| Kind of change | What to expect |
| --- | --- |
| **Factual correction** — a wrong number, a renamed command, a rule that changed | Merged as fast as a reviewer can confirm it. Say where you saw the truth (in game, in a patch note, in a screenshot) and it will be quick. |
| **Missing information** — a page that does not cover the case you hit | Welcome. Add it where it belongs rather than creating a new page, unless it is genuinely a new subject. |
| **New page** | Open an issue first, or write the PR with the reasoning in the description. One page = one subject. |
| **Style, tone, restructuring** | Discussion first. Not because style does not matter, but because two people rewriting the same paragraph in opposite directions wastes everyone's evening. Open an issue, agree on the direction, then write it. |
| **Typos, dead links, formatting** | Merged on sight. Batch them if you find several. |

If a PR sits for more than a few days without an answer, ping it — that is a review failure,
not rudeness on your part.

## The shape of a page

Every file under `content/` starts with a YAML front matter block. It is not decoration: the
site uses it to route the page, and CI rejects a page without it.

```yaml
---
title: Game basics — goal, evolution, threat, difficulty
slug: gameplay-basics
section: gameplay
order: 10
summary: What you are trying to do, the two numbers that drive the biters, and what difficulty changes.
updated: 2026-09-14
icon: "🎯"
legacy_anchors: [wiki-basics]
---
```

| Key | Required | Meaning |
| --- | --- | --- |
| `title` | yes | The page heading. Sentence case, no trailing period. |
| `slug` | yes | The public URL: `/index.php?r=docs/<slug>`. **Flat — no slashes.** Unique in the whole repo. Changing it breaks every link; add the old one to `redirect_from` instead. |
| `section` | yes | One of the ids in [`docs.yml`](docs.yml). |
| `order` | yes | Position in the section, ascending. Leave gaps of 10. |
| `summary` | yes | One plain sentence, ≤ 200 chars, shown on the index. |
| `updated` | yes | The date a human last checked the page against the game. Not the last edit date — git already knows that, and the site shows it. |
| `icon` | no | One emoji. |
| `legacy_anchors` | no | Anchors of the old single-page docs that used to point here, so stale links still land. |
| `redirect_from` | no | Former slugs of this page. |
| `vars` | no | The `{{placeholders}}` this page may contain (see below). |

The exact rules are in [`schema/front-matter.schema.json`](schema/front-matter.schema.json),
and `make check` tells you what is wrong in plain words.

## Writing rules

1. **No raw HTML.** The renderer strips it — your `<div>` will silently vanish from the
   site even though GitHub shows it. Markdown (CommonMark + GitHub tables, task lists and
   footnotes) covers everything these pages need.
2. **No `#` heading in the body.** The title comes from the front matter; start at `##`.
3. **One subject per page.** If a section is growing its own personality, it wants to be a
   page.
4. **Link to other pages by relative path to the `.md` file**, e.g.
   `[new-player strategy](strategy.md)` or `[coins](../bets/coins.md)`. That way the link
   works on GitHub *and* the site rewrites it to the right URL. Never hard-code
   `?r=docs/...` for a page in this repo.
5. **Images** go in `assets/`, referenced relatively (`![alt](../../assets/gameplay/wall.png)`),
   **≤ 500 KB each**, `.png` `.jpg` `.jpeg` `.webp` `.gif` only. No SVG (the site refuses to
   serve it — an SVG is a script container). Always write alt text.
6. **Wrap lines at about 90 characters.** Not a hard rule, but a 400-character paragraph on
   one line makes every future diff unreadable.
7. **Numbers need a source.** "Land mines are at −90%" is checkable in the scenario source;
   "land mines are weak" is not. Prefer the checkable form.
8. **Write for the player who just connected.** Assume Factorio knowledge, do not assume BB
   knowledge.

### Placeholders

A handful of numbers change without anyone editing a page — the size of a League queue, a
lock time. Those are written `{{like_this}}` and filled in by the site at render time. A
placeholder must be declared in the page's `vars:` list, and CI fails on an undeclared one.

You cannot add a new placeholder by yourself: the site has to know how to fill it. Open an
issue, and in the meantime write the literal value.

## Before you open the pull request

```sh
make check
```

It runs the same three checks as CI:

- **front matter** — every required key, right type, unique slug, known section;
- **internal links** — every relative link points at a file that exists (and at an anchor
  that exists, when you link to one);
- **images** — size budget and allowed extensions.

Plus `markdownlint`, which CI runs and which you can run locally if you have Node:
`npx markdownlint-cli2 "content/**/*.md" "*.md"`.

CI being green is necessary, not sufficient: it proves the page is well-formed, never that
it is true.

## Review

- [`CODEOWNERS`](CODEOWNERS) says who is automatically asked to review what. Reviewers are
  players and maintainers, not a company.
- A reviewer may ask for a source, split a PR, or merge a part of it. None of that is a
  judgement on you.
- Maintainers merge; nobody merges their own PR without a second pair of eyes, except for
  typos.

## Reporting instead of editing

[Open an issue](../../issues/new/choose). There are two templates: *factual error* and
*missing page*. Both are short. A screenshot of the game contradicting the docs is the
single most useful thing you can attach.

## Licence

By contributing you agree that your text is published under
[CC BY-SA 4.0](LICENSE), like the rest of this repository.
