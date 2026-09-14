---
title: Players online, stats and server status
slug: stats-and-status
section: site
order: 40
summary: Where the live player count comes from, and what the status page shows publicly and to admins.
updated: 2026-09-14
icon: "📡"
legacy_anchors: [sec-stats, sec-status]
---

## Players online & stats

- [Stats overview](https://biterbattles.org/index.php?r=stats/index)
- [Players online (live)](https://biterbattles.org/index.php?r=stats/playersonline) —
  per-team current playerlist, last refresh time.

The "online" count is the canonical source: refreshed every 60s from the game server. It is
what the homepage counter and the status page both read.

## Server status

[Status page](https://biterbattles.org/index.php?r=site/status) — auto-refreshes every 30s.

### Public sections

- Factorio uptime, players online, last finished game.
- Discord bots (radio TTS, hedwig, trustbot) state.
- BB Bets / Captain reconciler health.
- Tests state, network latency to NY/London/Paris/Frankfurt/Norway/Russia/Australia.

### Admin sections

Visible only when logged in via `askbot auth` as a player in the Factorio admin list:

- Long-running services + timer units (state, PIDs, last/next fire).
- System metrics (CPU/RAM/network/connections charts), top UDP peers, mtr per host.
- Recent journal errors (sanitized).
- Reconciler lag, drift, DB stats.
