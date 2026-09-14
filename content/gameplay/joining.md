---
title: Joining the server, spectator, trust, basic chat
slug: gameplay-joining
section: gameplay
order: 20
summary: How to connect, spectate, pick a side, earn blueprint trust, and the chat commands you need.
updated: 2026-09-14
icon: "🚪"
legacy_anchors: [wiki-joining]
sources: [https://freebb.miraheze.org/wiki/Main_Page]
---

## Connecting

- Multiplayer → look for **"Free BiterBattles.org"**, usually at the top by player count.
  Direct connect: `biterbattles.org:34197`.
- BB usually runs the latest stable Factorio (may lag a few days right after a release).
  Check the Discord `#announcements` if unsure.

## Being a spectator

You join as a spectator on the central island. **Spectating is the best way to learn**;
especially in 3+ vs 3+ games. Spectators see only the union of what both teams see — fog of
war still applies if neither team has radars.

By default, spectator chat goes to *both* teams. Ask questions, but don't dominate the chat.

## Joining a team

- If sides are equal — pick a side or auto-join (random).
- If sides are uneven — you can only join the smaller side.
- Once joined, your chat is team-only (+ spectators) by default.
- You can re-spectate by walking to the island and clicking the spectate button. Short
  cool-down before you can re-join your team. **You can't switch teams.**
- First thing to ask: "How can I help?" If alone, see the
  [new-player strategy](strategy.md).

## Solo / training mode (practice offline)

- Launch the BB scenario in single-player from
  `%appdata%\Factorio\scenarios\Factorio-Biter-Battles` (delete the folder + reconnect to
  the server to refresh it — it doesn't auto-update).
- Top-left red flag opens the **Team Manager**. Activate **training mode** there → science
  you send goes to *your own* team's biters instead of the enemy's. Threat gain stays paused
  until both teams have players.
- The cog menu enables specials (e.g. "Mixed Ore — Patches"); apply with
  `/instant-map-reset`.

## Trust system (for blueprints)

BP libraries / imports are disabled by design — copy-pasting intricate builds would kill the
game. New players also can't *use* in-game blueprints until they're "trusted":

- **Temporary trust** — any admin can grant in-game; resets on server reboot.
- **Permatrust** — automatic after **24 hours of total playtime**. Click the fish icon to
  see your hours. Tracking started early 2024, so older players may need a manual grant.

## Essential chat commands

- `` ` `` (backtick) — open chat.
- `/help` — full command list.
- `/admins` — list admins online. Be polite. Especially do what Fordeka tells you.
- `/spectator-chat`, `/nth`, `/sth`, `/shout` — see the
  [askbot cheatsheet section](../bot/askbot-commands.md).
