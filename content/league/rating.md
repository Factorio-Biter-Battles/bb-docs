---
title: BB League rating — OpenSkill
slug: league-rating
section: league
order: 15
summary: How your League rating works since 21 September 2026 — μ and σ, the number you see, provisional ratings, stakes, breaks and balanced teams.
updated: 2026-09-21
icon: "📊"
---

Since 21 September 2026 the BB League ranks players with **OpenSkill**, not Elo. Every
League game ever played was re-rated with it, so the history is the same; only the arithmetic
changed.

## What OpenSkill is

OpenSkill is an open-source rating system from the same family as Microsoft's TrueSkill
(Weng & Lin, 2011; the League uses its *Plackett–Luce* model). TrueSkill itself is patented
and a Microsoft trademark; OpenSkill does the same job and is free to use. ameateye and
janzert suggested it; janzert's comparison of rating systems is
[here](https://janzert.com/halite/rating-report/).

For every player it keeps two numbers:

- **μ (mu)**: your estimated skill. Everybody starts at 25.
- **σ (sigma)**: how unsure the system still is about μ. Everybody starts at 8.33. It shrinks
  as you play.

## The number you see

**rating = 950 + 20 × (μ − 3σ)**

`μ − 3σ` is a cautious estimate: the system is about 99.7 % sure your real skill is at least
that high. A new player has μ = 25 and σ = 8.33, so μ − 3σ = 0 and the rating shows **950**,
the same start as before.

Your profile shows μ and σ next to the rating (for example `μ 30.0 ± 4.0`). The no-show (−4)
and abandon (−8) penalties, see below, are added to the rating.

## Provisional ratings

Your rating exists from your first game, but with **fewer than 5 rated games** it is
**provisional**: the system still knows little about you and your σ is large.

- The leaderboard lists provisional players **below every ranked player**, with a
  **PROVISIONAL** badge (hover it to see your σ). They have no rank yet.
- Your profile shows `provisional — n/5 games` and how settled your rating is.
- After your 5th rated game the badge goes away and you get a rank.

## Why you go up by playing

Every game makes σ smaller, because the system learns more about you. A smaller σ raises
μ − 3σ, so a player who wins as often as expected still climbs slowly as their level gets
confirmed. Three lucky wins in a row can't put anyone on top: their σ is still large.

## Teams, upsets, 3–4 teams

- **Teams:** a team's strength is the sum of its players' μ, and its uncertainty is the sum of
  their σ². So 1v1 and 8v8 both work, and each player moves according to their own σ. A new
  player moves a lot, a veteran moves little.
- **Upsets:** the change depends on how surprising the result was. Beating a stronger lineup
  gives you more than beating a weaker one.
- **3 or 4 teams:** the game is rated on the final places, 1st to last, in one step (see
  [2, 3 or 4 teams](multi-team.md)).
- **What's at stake:** when a game starts, the in-game stake panel shows what each player can
  win or lose. In a 2-team game: each side's average rating, its chance to win, and the
  average move on a win or a loss. In a 3- or 4-team game: each team's chance to **finish
  1st**, and for every player the move if the team finishes **1st** or **last** (each player
  moves by their own σ). This is information only: your real change depends on your team's
  final place.
- **A team that didn't show:** it is taken out of the game. The teams that played are rated on
  their places among themselves. The players who didn't show get the no-show penalty; their
  teammates who did show get nothing.

## Taking a break

After 5 days without a game, σ grows a little every day, capped at the starting value. Your μ
doesn't change, but the cautious number slowly drops: about 1–2 points a day for a regular
player. Play one game and it comes back. This replaces the old activity bonus.

## Balanced teams

**BALANCED** teams (and the 3–4-team split) use your **estimated skill**,
`950 + 20 × (μ − 25)`, not the cautious number you see on the leaderboard. The team averages
in the BALANCED announcement are marked "(skill)" and may not match the leaderboard numbers.

## What did not change

- Signups, the lobby and the arrival rules.
- Which games count: the same rules as before, so games voided or cancelled by an admin still
  don't count.
- Coins for winning and for playing, and bets.
- The **−4 no-show** and **−8 abandon** penalties, now kept apart: they count in your rating
  but don't touch μ or σ.
- 5 rated games to appear in the ranking.

## FAQ

**Why did I lose places when the League switched?**
Usually one of three reasons:

1. **The old Elo started you above 950** thanks to the seeding sheet (Captain Games bonus).
   OpenSkill starts everyone at the same place and lets the games decide.
2. **You have few games.** Your σ is still large, so the cautious number is low. It rises as
   you play.
3. **Your wins came against weaker lineups, or your losses against weaker ones.** OpenSkill
   weighs every result by how surprising it was.

**Why did someone with fewer wins pass me?**
Their wins were against stronger lineups, or the system is more sure about them (smaller σ).

**Is the new system better at predicting games?**
On the 95 League games rated before the switch (21 September 2026), it predicted about as well
as the old Elo: a bit better in 1v1, a bit worse in team games. The difference is too small to
call yet. The League switched because OpenSkill handles teams of any size, 3–4-team games and
each player's uncertainty properly.

**Can I see my μ and σ?**
Yes: on your profile and in the leaderboard (`μ … ± …`).

**Will the numbers change again?**
Only through games, and through the slow drift after a long break.
