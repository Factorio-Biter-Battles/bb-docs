---
title: How BB League works
slug: league
section: league
order: 10
summary: The permanent ladder — say from how many players you play, the lobby starts the game, you get a DM, then teams, rating, gamble and fair play.
updated: 2026-09-21
icon: "📘"
---

The BB League is a permanent pickup ladder on the tournament server. You don't book a match:
you say when you're free and from how many players you'd play, and the lobby starts a game when
enough people are around.

## Before your first game

- Log in to the website (type `askbot auth` in a Free BB server's chat, then paste the token on
  the site).
- Link your Discord on your profile and keep **Main Discord notifications** on. The lobby
  reaches you by DM when your game starts; without that, it has no way to call you.

## Signing up

Use the red **BB LEAGUE** button in game (on any server), or the League page on the website.
Both show the same queue, live. You have **one signup, shared everywhere** — the last action
wins. Signing up again **updates** your window and keeps your place in the queue; *Leave*, or
your window running out, removes you.

- **I play from N players:** the smallest game you're happy to play, counting everybody. Games
  are 2 to 16 players: 2, 3, 4, 6, 8, 9, 10, 12, 14, 15 or 16. 5, 7, 11 and 13 can't be split
  into even teams, so they are never played. 3, 9 and 15 are always played as 3 teams.
- **When:** a start time and how long you're available, in 10-minute steps, from 30 minutes to
  6 hours. Times are shown in your own time zone.

The lobby shows how many players are waiting, and your own minimum. It never shows a team
count in advance: that is decided once the game is found.

⚠ Signing up is a commitment: a no-show costs the whole game for everyone else, hence the
penalty below.

## When your game starts

- The game starts as soon as enough players are waiting whose minimum allows that game size.
  The game is the biggest size that enough players' minimums allow. It is **first come, first
  served**: the queue order decides who is drawn, never the rating.
- Everybody drawn gets a **Discord DM** with the server to join.
- **Arrival:** all drawn players must be connected within 10 minutes (up to 20 minutes for the
  biggest games). If someone doesn't make it, the game is cancelled and **only the players who
  didn't show** lose a little rating (−4). Everyone else keeps their rating; sign up again to
  be drawn in the next game.
- **Team count:** if the number of players can be played with more than one number of teams,
  the players vote for it (see [2, 3 or 4 teams](multi-team.md)). If only one works, there is
  no vote.
- **Teams** (2-team games): everybody votes for how teams are made:
  - **RANDOM**: a coin flip.
  - **BALANCED**: teams as even as possible by rating, and always the same result for the same
    players.
  - **MANUAL**: players pick their own sides.

  A mode wins when two thirds of the players choose it (with 4 players or fewer, everybody). A
  1v1 skips this step. With 3 or 4 teams the teams are always the balanced split.
- Players can vote to kick someone during team formation. The game is then cancelled and the
  others go back to the queue.

In a 2-team game the standard ceremony follows: **2 map rerolls per side**, sides drawn at
random, map locked, and the prep countdown — skippable when *all* players click go. With 3 or 4
teams, players are placed on their squares automatically (see
[2, 3 or 4 teams](multi-team.md)).

**Official Cup matches come first:** the lobby pauses before them, and a League game still
running close to an official match is voided, with **no rating change** for anyone.

You can also be signed up in the [BB Captains](../captain/bb-captains.md) lobby at the same
time; you are never drawn by both at once.

## Your League rating

**Individual**, shown in the red button, on the leaderboard and on your profile. Since 21
September 2026 it is an **OpenSkill** rating that starts at **950**; how it moves is explained
on its own page: [BB League rating](rating.md).

A game **counts the moment the map locks** and the game starts; leaving mid-game doesn't save
you — you share your team's result. Only a **fresh map** counts: a game that started before
your match fired (an old map whose silo already fell) is never ingested — the server resets
and your match starts on a new one. A game of 25 minutes or more also pays a small
participation reward.

| Event | Rating |
| --- | --- |
| A rated game (2, 3 or 4 teams) | moves with the result — see [BB League rating](rating.md) |
| No-show after your game fired | −4 |
| Game abandoned by both sides | −8 everyone |
| Game voided before an official match | no change |
| Cup / tournament match | 0 — never moves it |

You need **5 rated games** to be ranked on the leaderboard; before that your rating is shown as
**provisional**.

**Coin gamble.** While the teams get ready, everyone on the server can put coins on a side
(in-game panel; in a 3- or 4-team game you bet on the team that finishes 1st); the pot is
shared by the winning side in proportion to their stakes, a bet can never win more than the
other side put in, and stakes are refunded if the match is cancelled.
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
