# BB Docs — the documentation of biterbattles.org

[![CI](https://github.com/Factorio-Biter-Battles/bb-docs/actions/workflows/ci.yml/badge.svg)](https://github.com/Factorio-Biter-Battles/bb-docs/actions/workflows/ci.yml)
[![License: CC BY-SA 4.0](https://img.shields.io/badge/license-CC%20BY--SA%204.0-lightgrey.svg)](LICENSE)

Every guide, rule and tip you read on **biterbattles.org** lives here, in Markdown. The
website renders these files directly: merge a pull request, and it is on the site within
five minutes.

## Contribute in five lines

1. Find the page under [`content/`](content/) — or click *Edit this page on GitHub* at the
   bottom of any page on the site, which opens the right file for you.
2. Edit the Markdown. Keep the front matter block at the top intact.
3. Open a pull request. CI checks the format; a human reads the content.
4. A maintainer merges it. **Nothing is ever auto-merged.**
5. The site picks it up on its next pull, within five minutes.

Not sure enough to edit? [Open an issue](../../issues/new/choose) — "this is wrong" with a
sentence of explanation is a perfectly good contribution, and it is how most corrections
start.

## What is in here

| | |
| --- | --- |
| [`content/`](content/) | The pages, one Markdown file per subject, grouped by section. |
| [`docs.yml`](docs.yml) | The list of sections and their order. Pages are **not** listed here — each page carries its own front matter. |
| [`assets/`](assets/) | Images, referenced from pages by relative path. |
| [`schema/`](schema/) | The JSON schema the front matter must satisfy. |
| [`tools/`](tools/) | The checks CI runs, and a local preview. Plain PHP, no dependencies. |

## What is **not** in here

The website's application code, and the server infrastructure. That half holds the RCON
credentials of the live game servers, the Discord OAuth secrets, the coins and betting
engine and the admin tooling — publishing it would hand anyone the keys to the game servers
and to people's coin balances. That is the only reason, and it is not going to change.

The game itself is a different project and is fully open source under GPL-3.0:
[Factorio-Biter-Battles](https://github.com/Factorio-Biter-Battles/Factorio-Biter-Battles).

## Previewing your change

The GitHub preview of a Markdown file is faithful enough for almost every edit — tables,
task lists and footnotes all render the same way the site renders them. If you want the real
thing, with the site's stylesheet:

```sh
make preview          # needs PHP 8.1+ ; serves http://127.0.0.1:8088
```

`make check` runs exactly what CI runs:

```sh
make check            # front matter + internal links + image budget
```

Both are plain PHP scripts in [`tools/`](tools/) with no dependencies to install. The one
thing they cannot check is whether a sentence is *true* — that is what the review is for.

## House rules, in short

- **One page, one subject.** A page is a thing someone came looking for.
- **Facts beat prose.** A correction with a number in it gets merged fast. A rewrite for
  style gets a conversation first.
- **No HTML.** The site strips raw HTML from Markdown before rendering it, so it would
  silently disappear. Everything you need exists in Markdown.
- **Images ≤ 500 KB**, in `assets/`, referenced by relative path, with alt text.

The long version, including how the review works and who reviews what, is in
[CONTRIBUTING.md](CONTRIBUTING.md).

## Sources & attribution

The **gameplay** pages (`content/gameplay/`) and **Captain games**
(`content/captain/captain-games.md`) are *adapted from the
[Free Biter Battles Wiki](https://freebb.miraheze.org/wiki/Main_Page)*, which is itself
published under **CC BY-SA 4.0** and written by its contributors — the wiki was started by
**proxc**. The articles this text draws on include *Introduction*, *How to start playing*,
*New player strategy guide*, *Threatfarming*, *Suggested research orders*, the weapon-upgrade
tables and *Captain*. Wording has been edited, reordered and updated against the current
scenario source; any mistake in this version is ours, not the wiki's.

Each adapted page names its source in its own front matter (`sources:`) and the website
prints it in the page footer. If you recognise your own writing here and want the credit
stated differently — or removed — open an issue and we will fix it the same day.

Everything else (BB Bets, BB Coins, the API, the bot reference, BB League and BB Cup, the
server and site pages) was written for biterbattles.org.

## Licence

Content is [CC BY-SA 4.0](LICENSE) — reuse it, adapt it, share it back under the same
licence, and credit the Biter Battles community and the Free Biter Battles Wiki where a page
names it. By opening a pull request you agree to licence your contribution the same way.
