---
title: Askbot cheatsheet — every in-game command
slug: askbot-commands
section: bot
order: 20
summary: Every askbot command, plus the Factorio chat and admin commands that are worth knowing.
updated: 2026-09-14
icon: "📜"
legacy_anchors: [sec-cheatsheet]
---

Type these in the in-game chat. During a captain game, prefix with `/spectator-chat` to
whisper the bot privately.

## Auth & identity

- `askbot auth` — get a one-shot token to log into the website.
- `askbot data <player>` — public PnL / coins / bets summary.
- `askbot vs <playerA> <playerB>` — head-to-head over the last 90 days: games together,
  games faced, who won. Full top-10 allies / rivals on the player profile.

## BB Coins

- `askbot coins` — your balance.
- `askbot coins tip <player> <amount>` (alias: `send`) — tip, min 0.1c.

## BB Bets — read-only / informational

- `askbot market list` · `info <mid>` · `orders <mid>` · `shares` · `myorders` · `burned`
- `askbot market preview buy|sell <mid> <opt> <amt>`
- `askbot market help trade|lp|fees|create|info|admin`

## BB Bets — actions

- `askbot market create winner|duration|custom …` — open a market.
- `askbot market cancel <mid>` — cancel (creator if no trades, or admin).
- `askbot market lp add|remove <mid> <amt|all>` — provide / withdraw liquidity.
- `askbot market resolve <mid> <opt>` — admin-only force resolve.
- **Trading (buy/sell/limit) is web-only.** Use `askbot auth` then the website.

## Science / ELO

- `askbot science topscore` · `science <player>`
- `askbot challenge` · `challenge top`
- `askbot elo`

## Factorio chat / control commands (non-askbot)

- `` ` `` (backtick) — open chat / command window.
- `/help` — list of all chat commands.
- `/admins` — list admins online (ping politely if you need trust).
- `/spectator-chat <msg>` — whisper spectators only (also used to message askbot privately
  during a captain game). Tab-complete: `/spe[tab]`.
- `/nth <msg>` · `/sth <msg>` — chat to north / south team only (available to spectators
  too).
- `/shout <msg>` — message both teams.
- `/calc-send force=<n|s> color=<sci> count=<n>` — preview evolution / threat impact of
  sending science. Auto-fills evo, difficulty, players from current game state. Color
  aliases: `red`=automation, `green`=logistic, `gray`=military, `blue`=chemical,
  `purple`=production, `yellow`=utility, `white`=space. Full form:
  `/calc-send evo=20 difficulty=30 players=4 color=green count=1000`.
- `/i <category>` — scan inventories for items by category (e.g. `/i ammo`, `/i defense`).
  Great for finding who's hoarding.
- `/where <player>` · `/follow <player>` — locate / spectate-follow a player.
- `/clear-corpses` — wipes biter corpses (run after big fights to reduce lag).
- `/crafting-list` — toggle the personal crafting-queue window.
- `/inventory-costs [all]` — top players by inventory value (`all` = both forces).
- `/jail <player>` · `/suspend <player>` — admin-only. Suspend = forced spectator for
  10 min.
- `/announce` · `/announce-append` — admin-only, posts text on spectator island.
- `/difficulty-revote` · `/difficulty-close-vote [<name|%>]` — admin-only, re-open / close
  difficulty vote.
- `/scenario restart|shutdown|restartnow` — admin-only, soft-reset / shutdown control.
- `/instant-map-reset [seed]` — solo-mode (or admin), force a reset; pass a seed
  (341–4294967294) or omit to print current.
- `/current-map-seed` — print the current map seed.
- `/set-pathfinder bb-default|bb-new|default` — admin-only, switch pathfinder preset at
  runtime.

## Server connect

- Multiplayer → "Free BiterBattles.org" (top of the list when sorted by player count), or
  add to favourites: `biterbattles.org:34197`.
- The local solo copy at `%appdata%\Factorio\scenarios\Factorio-Biter-Battles` doesn't
  auto-update — delete the folder and reconnect to refresh.
