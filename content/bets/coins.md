---
title: BB Coins — wallet, transfers, history
slug: coins
section: bets
order: 10
summary: The soft currency you earn for time spent on the winning team — how it is earned, where to look, how to tip.
updated: 2026-09-14
icon: "💰"
legacy_anchors: [sec-coins]
---

BB Coins are a soft currency earned passively for time spent on the winning team. They
power the BB Bets prediction markets and can be tipped peer-to-peer.

## How you earn coins

- **Win bonus** — 1c per 4 hours of winning-team active playtime (engine-tick based).
  Captains: 5× (winner) / 2.5× (loser). Credited 5–7 min after a normal game ends,
  ~30s–2 min after a captain game.
- **MVP awards** — top-3 voted contributors on each team, in games ≥ 25 min and avg 8+
  players (top1 = 3× rate, top2 = 2×, top3 = 1×).
- **BB Bets** — winning trades, LP fee payouts, market resolution payouts.
- **Tips received** from other players.

## Where to look

- [My account](https://biterbattles.org/index.php?r=profile/me) — Discord link, BB League
  status, private settings (requires askbot auth login).
- Your public profile — your balance, transactions, BB Bets activity, captain stats,
  science / eff. Reachable from your account page, or from any player name on the site.
- [BB Explorer](https://biterbattles.org/index.php?r=science/bb-explorer) — global
  leaderboard, all transactions, top earners.

## In-game commands

- `askbot coins` — your balance.
- `askbot coins tip <player> <amount>` (alias: `send`) — peer-to-peer tip, no fee,
  min 0.1c.
- `askbot data <player>` — public profile (PnL, last 10 transactions, win/loss bets).

A full account of where the currency comes from, what it is worth and how it is kept from
inflating is in [how the betting system actually works](betting-system.md).
