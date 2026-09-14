---
title: Draft analytics — APP, PVAP, tiers T1–T5
slug: draft-analytics
section: captain
order: 20
summary: How the captain page decides who is over- or under-drafted — average pick position, pick value above position, and the tier quintiles.
updated: 2026-09-14
icon: "📐"
---

The [Draft analytics](https://biterbattles.org/index.php?r=captain/index#draft-wrap) section
on the captain page surfaces who is over- or under-drafted relative to their actual win
contribution. Three numbers do the work:

- **APP** (*Average Pick Position*) — the player's mean draft slot across all captain games
  they've been picked in, with the captain seat (slot 1) excluded. Lower APP = picked
  earlier = considered more valuable by captains. Only players with at least
  `DRAFT_MIN_PICKS` picks are scored.
- **Slot win rate** — the league-wide win rate of picks at each integer slot (2, 3, 4, …).
  Slot 2 wins more than slot 9 on average; we measure each player against that baseline.
- **PVAP** (*Pick Value Above Position*) = player's win rate − slot win rate at round(APP).
  Positive = wins more than someone usually picked at that slot would; negative = wins less.
  PVAP is the headline metric for sleepers/reaches.

**Tiers T1–T5** are pure *APP quintiles* over the qualifying pool — nothing to do with win
rate, skill, or PVAP. Computation:

1. Collect APP for every qualifying player (≥ min picks, non-captain).
2. Sort ascending. With *N* players, the four cutoffs sit at indices `floor(N×1/5)`,
   `floor(N×2/5)`, `floor(N×3/5)`, `floor(N×4/5)`.
3. **T1** = APP ≤ 1st cutoff (top quintile, picked earliest) · **T2** ≤ 2nd · **T3** ≤ 3rd ·
   **T4** ≤ 4th · **T5** = the rest (picked latest).

Tiers are then mapped onto every player (including small-sample ones) using the same
cutoffs, so the broader table uses the same buckets. Sleepers are flagged when tier ∈ {4, 5}
(picked late) *and* PVAP > 0; reaches when tier ∈ {1, 2} (picked early) *and* PVAP < 0.

**Captain Draft IQ** = per-captain average of the PVAP of every player they ever picked.
Positive → consistently spots sleepers; negative → consistent reaches. Independent of which
side of the draft was theirs — it's measured against the league slot baseline.
