---
title: Game basics — goal, evolution, threat, difficulty
slug: gameplay-basics
section: gameplay
order: 10
summary: What you are trying to do, the two numbers that drive the biters, and what difficulty actually changes.
updated: 2026-09-14
icon: "🎯"
legacy_anchors: [wiki-basics]
sources: [https://freebb.miraheze.org/wiki/Main_Page]
---

Biter Battles is a **scenario** ("soft mod") originally written by M3wM3w at Comfy Factory.
Two teams (north / south) face off across a central river. **You win when the enemy silo
dies.** You make the enemy biters stronger by feeding science flasks to your own team's
scoreboard — those flasks raise the enemy biters' evolution and threat. It's an
indirect-PvP speedrun: build fast, build compact, every tile you defend is a tile you have
to wall.

## Two metrics drive the biters

- **Evolution (evo)** — what biters spawn and how strong they are. Above 100% is possible
  (boosts HP and damage). Milestones (approximate):
  - 17% — medium biters appear
  - 50% — big biters (blue) appear
  - 90% — behemoths (green) appear
- **Threat** — how big the next attack waves are. Every 2 minutes, 7 waves spawn, split
  between both teams in proportion to their threat. **If your threat is negative, no waves
  spawn against you.** Killing biters lowers threat. Threat increases passively over time
  and every time the enemy sends science.

Use the in-game `/calc-send` command to preview how a given send affects evo and threat —
it auto-fills evo, difficulty and player count from the live game.

## Difficulty = mutagen effectiveness (7 levels)

"Difficulty" is how effective *science sends* are, not how fast biters get strong on their
own. Values from `tables.lua`:

| Level | Short | Mutagen |
| --- | --- | --- |
| I'm Too Young to Die | ITYTD | 20% |
| Have a Nice Day | HaND | 35% |
| Piece of Cake | PoC | 50% |
| Easy | Easy | 75% |
| Normal | Normal | 100% |
| Hard | Hard | 200% |
| Fun and Fast | FnF | 500% |

On Normal, lower-tier sciences (green, military) readily bring opponents to 50% evo (big
biters → flamers required). On ITYTD, mutagen is only 20% as effective — those games
typically run to multiple rocket sends. Effectiveness slowly rises after 3 hours so games
eventually end.

## How much do I need to send?

At 100% difficulty, January 2025 data:

- 50% evo (big biters) ≈ **64 white-sci-equivalents**.
- 70% evo ≈ 200 equivalents.
- 100% evo ≈ 1200 equivalents.
- Above 90%, every 1000 space science adds ~16.5% evo.
- At HaND (35% effectiveness), multiply by ~2.85; at ITYTD (20%) multiply by 5.
- Player count doesn't change evo % per send, but threat increase scales linearly: 125
  modifier at 0 players → 250 at 20+ players (capped).

## By the numbers — mutagen strength & threat values

From `tables.lua` / `ai.lua`. Each science type contributes "mutagen strength" per flask.
Bigger sciences punch ridiculously harder than reds:

| Science | Mutagen / flask | vs red |
| --- | ---: | ---: |
| Automation (red) | 9 | 1× |
| Logistic (green) | 23 | 2.6× |
| Military | 95 | 10.6× |
| Chemical (blue) | 292 | 32× |
| Production (purple) | 1050 | 117× |
| Utility (yellow) | 2205 | 245× |
| Space (white) | 4375 | 486× |

Threat farmers: each kill removes a fixed amount of threat from your team's pool:

| Target | Threat removed |
| --- | ---: |
| Small biter / spitter | 1.5 |
| Medium biter / spitter | 4.5 |
| Big biter / spitter | 13 |
| Behemoth biter / spitter | 38.5 |
| Small / medium / big / behemoth worm | 8 / 16 / 24 / 32 |
| Biter / spitter spawner | 32 |

## Late-game pain (past 100% evo)

- **Reanimation** — biters above 100% evo can reanimate after dying. Chance scales
  linearly: at evo 150% with max threshold 350, you get ~42% reanim rate. **Capped at
  90%.**
- **Boss biters** — special wave members get **26× normal HP** (config:
  `health_multiplier_boss = 20 × 1.3`). Half of late-game waves are bosses.
- **3× threat sends past 100% evo** — every science flask sent above 100% evo gives the
  enemy 3× more threat than the same flask at 99% (`threat_scale_factor_past_evo100`).

## Fish — the secret OP item

- **Eat** a fish to instantly heal and lead spitters into your turrets. Always carry some.
- **Send** fish to spy: each fish reveals the enemy side for 45 seconds. Click the fish
  shortcut: **LMB = 1**, **RMB = 5**, **Shift+LMB = all**, **Shift+RMB = half**.
- Fish are always allowed to be sent — even when the science-send build-score restriction
  is on.

## Recent BB scenario tweaks (2026)

- **Science send build-score restriction** (admin toggle, April 2026) — players need a
  minimum number of placed entities before they can send science. Required =
  `ceil(48 / difficulty_value)`: ITYTD = 240, HaND = 138, PoC = 96, Easy = 64, Normal = 48,
  Hard = 24, FnF = 10. Fish always allowed.
- **Captain research lock** (April 2026) — captains can lock the research queue and
  whitelist which teammates may add to it.
- **Classic vs advanced pathfinding** (April 2026, classic default) — admin toggle.
  Classic = simpler / more direct biter paths + extra structures (turrets, radars) on the
  biter target list. Indicated by an icon in the top bar; warning shown when entering
  captain mode with classic on.
- **Daytime always-day by default** (March 2026) — admin can revert to the day/night cycle.

## Main vs outposts

- **Main** = the silo. Map gen guarantees the big ores nearby (no uranium); the immediate
  area is refined concrete for speed.
- **Outposts** = anything 500–3000 tiles out. Bigger / richer ore + oil patches, lower (not
  zero) biter pressure. Solo outposts as a new player are usually a bad idea — you'll die,
  respawn in main, and find your outpost already gone.

## Versus vanilla Factorio

- No main bus — too much area to defend.
- Attacks *never stop* while threat > 0; you can't just kill nearby spawners and idle out.
- Hand-fed builds are viable. "Burner city" is the go-to early strategy on the highest
  difficulty.
- White science unlocks via rocket-silo tech (the space-science tech itself is useless).
