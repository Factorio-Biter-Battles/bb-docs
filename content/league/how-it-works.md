---
title: How BB League works
slug: league
section: league
order: 10
summary: The permanent ladder — signing up from three surfaces, automatic matchmaking, what happens when your game fires, and how League Elo moves.
updated: 2026-09-14
icon: "📘"
vars: [cap1v1, cap2v2, cap3v3, cap4v4, cap5v5, cap7v7, cap8v8]
---

## Signing up

Sign up from any of the **3 surfaces**: the website (the League panel on the BB League
page — mouse slider, from/until shown in your local time *and* UTC), the **MAIN** Factorio
server, or the **TOURNAMENT** server — in game it is the red **BB LEAGUE** button,
top-left.

You must have your **Discord linked** — that is how you get pinged the moment your game
fires.

You have **one signup, shared across all surfaces** — the last action wins everywhere.
Clicking *Join* again **updates** your window (you keep your queue spot); *Leave* — or your
window expiring — removes you.

Format checkboxes: **3v3** and **5v5**, both on by default; **1v1, 2v2, 4v4, 7v7 and 8v8 are
opt-in** (off by default). Tick every format you are happy to play — you are pooled for each
of them at once. At least one must stay ticked.

⚠ Signing up is a commitment: a no-show costs Elo (see below).

## Matchmaking — fully automatic

**First come, first served**, strictly by signup order — no skill matching, no queue
dodging.

A format fires the moment its pool is full among players whose window is open: **1v1 at
{{cap1v1}}, 2v2 at {{cap2v2}}, 3v3 at {{cap3v3}}, 4v4 at {{cap4v4}}, 5v5 at {{cap5v5}}, 7v7
at {{cap7v7}}, 8v8 at {{cap8v8}}**. When more than one is full, the **biggest game wins the
slot** — 8v8 > 7v7 > 5v5 > 4v4 > 3v3, then 2v2, then 1v1 — because only one League game runs
at a time and the bigger fire gets more of the queue playing. In a **2v2 the 4 players must
agree unanimously** on the teams (RANDOM needs 4/4; MANUAL is armed by all 4 with an exact
2/2 split); a **1v1 has no team formation at all** — the sides are drawn the moment both
players are in. Bigger parties get a little longer to arrive and to decide (up to 20 and 15
minutes at 8v8).

**Official matches own the server:** from 120 minutes before a scheduled Cup match until its
post-game map reset, no League game can start — you'll see a red notice. If your window is
still open after the official match, your game fires then, automatically.

## When your game fires

You get a **Discord DM instantly** (and a connect prompt if you're on the main server). You
then have **10 minutes** to show up on the tournament server — each absent player loses
**4 Elo** (the ones who came lose nothing) and the game is cancelled; everyone re-queues by
re-clicking.

Once all players are there: **team formation** — a public panel where the players choose
**RANDOM** teams (fires at a 2/3 majority) or **MANUAL** (pick your own side, exact split
required, everyone clicks ARM to confirm; any change resets the arms; 10-minute limit or the
game is cancelled, no Elo).

Then the standard ceremony: **2 map rerolls per side**, sides drawn at random, map locked,
and the prep countdown — skippable when *all* players click go.

## Your League Elo

**Individual**, starts at **1000**, shown in the red button, on the leaderboard and on your
profile. **Zero-sum:** winners gain exactly what losers lose — the population average stays
1000 forever.

One game moves everyone on both teams by the **same amount**: 32 × (1 − expected win
chance), clamped between 1 and 31 — beating a stronger lineup pays up to +31, beating a much
weaker one +1. Team strength = **average Elo** of its players. **1v1 games move half as much**
(16 × instead of 32 ×, so ±0.5 to ±15.5): the easiest format to farm weighs half.

**Worked example** (a real early game): winners averaged **1010.6**, losers **984.1**.
Expected win chance = 1 / (1 + 10^((984.1 − 1010.6)/400)) = **53.8%** → Δ = 32 × (1 − 0.538)
= **±14.78** for all six players. Every rated game's page shows this exact breakdown in its
*📈 League Elo* section.

**Why early games all move ~16:** while everyone hovers near 1000, team averages can only
differ by a few points, so the expected chance sticks to ~50% and Δ to ~16. The spread does
the work later:

| Gap between team averages | Favorite wins | Underdog wins |
| --- | ---: | ---: |
| 0 (even lineups) | ±16 | ±16 |
| 50 | ±13.7 | ±18.3 |
| 100 | ±11.5 | ±20.5 |
| 200 | ±7.7 | ±24.3 |
| 400 | ±2.9 | ±29.1 |
| 600+ | ±1 (floor) | ±31 (cap) |

A game **counts the moment the map locks** and the game starts; leaving mid-game doesn't
save you — you share your team's result. Every winner also gets **1 coin** per game. Only a
**fresh map** counts: a game that started before your match fired (an old map whose silo already
fell) is never ingested — the server resets and your match starts on a new one.

| Event | Elo |
| --- | --- |
| Win against a stronger lineup | up to +31 |
| Win against a much weaker lineup | +1 |
| Loss (mirror of the winners' gain) | −1 to −31 |
| No-show after your game fired | −4 |
| Game abandoned by both sides | −8 everyone |
| Cup / tournament match | 0 — never moves it |

You need **5 played games** to be ranked on the leaderboard — your rating works from game 1.

**Coin gamble.** While the teams get ready, everyone on the server can put coins on a side
(in-game panel); the pot is shared by the winning side in proportion to their stakes, a bet can
never win more than the other side put in, and stakes are refunded if the match is cancelled.
Players can only back their own team. If the **same two line-ups meet again within 90 minutes**,
the match is played and rated as usual but has **no gamble**.

**Fair play.** One Discord account per League account (the link is required to queue, and a
Discord can only be linked once). Two accounts seen on the same address never form a 1v1, and
the same two players cannot meet twice in a row in 1v1. Accounts that play each other from the
same address are flagged for review; farmed games are reverted, coins included.

## After the game

Same treatment as official matches: result + top-builders **recap card on Discord**, full
stats on the site (game page, profiles, leaderboard), and you're invited back to the main
server a few minutes later.

Terms like Swiss, joker or bye are defined in the [glossary](glossary.md).
