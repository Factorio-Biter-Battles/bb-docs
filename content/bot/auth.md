---
title: Logging in with askbot auth
slug: askbot-auth
section: bot
order: 10
summary: There are no passwords on the website — you log in as your in-game player, with a token the in-game bot whispers to you.
updated: 2026-09-14
icon: "🔑"
legacy_anchors: [sec-auth]
---

Most website features (BB Bets trading, BB Coins transfers, admin actions on markets,
future features) require being logged in **as your in-game player**. There are no passwords
on the website — auth happens through the in-game chat bot.

## Steps

1. In Factorio chat, type `askbot auth` (or `/spectator-chat askbot auth` during a captain
   game).
2. The bot whispers you an 8-character token, valid for 10 minutes.
3. Open the [BB Bets page](../bets/overview.md), paste your in-game name + the token, click
   login.
4. You're now session-authed for 30 days as that player.

Requires 1+ day of total playtime. The token is one-shot and expires fast — get a fresh one
if you take too long.
