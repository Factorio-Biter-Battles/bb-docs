---
title: BB Cup — format, duties, scenario and rewards
slug: cup-rules
section: league
order: 20
summary: The 3v3 tournament rule book — Swiss format and scheduling, what each player has to do, the starter pack and difficulty ramp, the rewards.
updated: 2026-09-14
icon: "🏆"
legacy_anchors: [tt-help]
vars: [team_list_lock]
---

## Format & schedule

- **Format:** 3 vs 3, on a separate dedicated server (not the public game). **Swiss league —
  4 rounds, every team plays at least 4 matches** (no knock-out after a single game), then
  the **Top 4 advance to single-elim playoffs** (semis + final).
- **Match length goal:** aim **60–90 minutes**, hard cap around **2h**. 3v3 is intense —
  long matches drain everyone.
- **Scheduling (fully automated):** a weekly *availability grid* (Doodle-style) on the site.
  Each player marks slots **✓ available / ~ if needed / ✗ no** for the coming round. It
  locks **Sunday evening** (the exact instant, in your own timezone, is on the 🔒 lock chip
  at the top of the Cup page), then the
  system auto-pairs teams (same win/loss bracket) and picks a time both teams can actually
  make — no captain has to chase anyone for a date. Fill it weeks ahead or round by round.
  Matches never overlap the weekend captain games (hard rule). **Need to move a match?** Your
  captain proposes a new slot from "Your matches" and the opposing captain accepts (grid
  slots only, server-conflict checked) — or, at match time, both teams type
  `askbot tt postpone` during the reroll session. Keep it exceptional: the joker and spares
  exist for a reason.
Cups are scheduled in the gaps around BB Masters — 3v3 matches never clash with the weekend
captain games, and are played on a separate dedicated server.

### Summer Cup 2026 (finished)

The first cup run under these rules, kept here as the worked example of the calendar shape:
the team list locked **{{team_list_lock}}** (together with the round-1 availability lock —
player registration and substitute additions stayed open all tournament), first matches the
week of **20 July 2026**, four weeks of Swiss, playoffs and final around the **end of
August 2026**.

**The dates of the cup that is actually open are on the Cup page, not here** — this page is
the rule book, and rules outlive a calendar.

## Your duties

- **Build a team:** create one (you're captain), or wait for an invite. Captain invites with
  `askbot tt invite <player>`; invitees reply `askbot tt accept <team>`. **Rosters hold 3 to
  5 players** — 3 play each match, up to 2 spares. The line-up is simply whoever is available
  at the scheduled slot, so spares fill the availability grid like everyone else. More spares
  = easier scheduling. **Substitutes can be added at ANY time of the tournament**, even on
  match day — they just need to be registered and Discord-linked (one team at a time;
  captains can kick to make room).
- **Your part:** mark at least **8 slots (4 firm ✓)** each round — **can't offer 8? two
  shortcuts count as a FULL commitment: all four weekend slots (3 firm ✓) for NA timezones,
  or all five slots of a single weekday (14:00→22:00, 4 firm ✓) if you can only play one
  day** — and make sure your team ends up with **≥4 common slots including ≥2 in an anchor
  band** — either the evening band (the 18:00 weekday slot, highlighted in the grid) **or the
  weekend slots** (whichever fits your timezones; matches are actually scheduled on ANY slot
  both teams share, the band is just the eligibility anchor). Aim for **5+ commons incl. 3 in
  your band**: that's what guarantees an opponent shares a slot with you. The bot only pings
  you if you haven't committed your grid. Each team has **1 joker** for the whole tournament
  to skip a round (counts as neither win nor loss). Miss a round with no joker left = forfeit
  that round.

## Scenario & rules

- **Starter pack (identical for both teams, per player unless noted):** 10 grenades · 3× the
  normal starting concrete · 10 burner miners · 10 furnaces · 200 power poles · 100 coal ·
  20 fish · boilers/steam power box (~11 MW per team) · electric-miner tech pre-unlocked.
  **Laser bonus:** the first laser turret a team places grants it a one-time **+50 MW**
  (supports late-game / rocket pushes).
- **Movement boost:** *free concrete on the floor* in the starting zone so builders move fast
  from minute zero — small thing, big QoL gain at the 3v3 scale.
- **Biter AI:** looking into the *easy old biter AI* variant (cojito's, already used on
  regular BB) — biter waves are more predictable early so teams can settle faster.
- **Map rerolls (fully automated):** the match map spawns **10 min before start** and the
  *reroll session* opens — match players must be there to veto. Each team has **5 rerolls**:
  any player of the match types `askbot tt reroll` in the game chat. Out of rerolls? **Both**
  teams typing `askbot tt reroll-wtf` rerolls again, as often as needed. Happy with the map?
  **Both** teams type `askbot tt reroll-ok` to lock it. At the scheduled hour, sides
  (north/south) are **drawn randomly**, players are moved onto their side and **frozen** (you
  can walk and plan; mining/building/crafting unlock at GO), then **10 minutes of prep** —
  and GO. Both teams ready early? Both type `askbot tt go` during the prep to start
  immediately. If the map still isn't locked **30 min past the scheduled hour**, the match is
  **cancelled and both teams take a loss** (bets refunded), so don't troll the reroll phase.
- **Difficulty:** **custom 80%** (between Easy and Normal). **Ramp:** from **60:00**
  difficulty rises **+5%/min**, slowing to **+3%/min** once either team has placed a laser
  turret — the clock itself ends matches around the 90-minute mark. **Retroactive send:**
  science sent early is automatically re-valued as difficulty ramps, so sending early vs late
  yields the same evolution (only threat timing stays a tactical choice). **Rocket:**
  launching counts as ×3 white-science value. All tech kept, all science tiers allowed. Rules
  are established; fine-tuning stays possible mid-tournament if something proves broken.

## Rewards

- **Reward:** every winning match pays **10c per player × 3 = 30c**, auto-paid by the bot
  when the BB Bets winner-market for the match resolves.
- **Betting:** every fixture gets its own BB Bets winner-market — the 🎲 links in the
  calendar and on the match pages. Spectators bet, players get paid on the result.

Terms such as *slot*, *anchor band*, *gate* and *joker* are defined in the
[glossary](glossary.md).
