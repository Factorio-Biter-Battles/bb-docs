---
title: BB League — 2, 3 or 4 teams
slug: league-multi-team
section: league
order: 12
summary: The team-count vote, the four-square map, placement, late arrivals, science targeting, elimination, rating and bets in 3- and 4-team games.
updated: 2026-09-21
icon: "🗺️"
---

A League game can be played between **2, 3 or 4 teams** on the same map. With 2 teams it's the
Biter Battles everyone knows. With 3 or 4, the map and a few rules change.

## Choosing the number of teams

Once a game has been found, the players **vote** for the number of teams. Only the numbers the
player count can be split into are offered. A team has at most 8 players with 2 teams and at
most 5 with 3 or 4 teams. For example:

- 12 players → 2 × 6, 3 × 4 or 4 × 3
- 8 or 16 players → 2 or 4 teams
- 6 players → 2 or 3 teams
- 3, 9 or 15 players → 3 teams only, so there is **no vote**

How the vote works:

- Every player connected to the server gets the vote window, including someone who joins while
  it is open. You can change your vote until it closes.
- The vote lasts **45 seconds**. The option with the most votes wins, and a tie goes to the
  smaller number of teams.
- If nobody votes, or the vote doesn't finish in time, the game uses the **smallest possible
  number of teams** (2 whenever 2 works).

With 2 teams, the teams are then formed as usual (random, balanced or manual — see
[How BB League works](how-it-works.md)). With 3 or 4 teams, the teams are always the
**balanced split**: players are sorted by rating and dealt in snake order (team 1, 2, 3, 4,
then 4, 3, 2, 1, …), so the teams stay even and the same players always give the same teams.

## The map

- The map is cut into **four squares by a cross of rivers**, with the spectator island in the
  middle.
- Teams are named after their square: **Top Left, Top Right, Bottom Right, Bottom Left**.
- With **3 teams**, the **Bottom Left** square is deep water and belongs to nobody. The teams
  are Top Left, Top Right and Bottom Right.
- Every square is the same base, turned a quarter turn: silo, spawn wall, ores, and the
  biters' area behind it.
- No landfill on the rivers. Robots, ghosts and deconstruction only work in your own square.
- The **research info** window lays out the teams as they sit on the map: a 2 × 2 grid with 4
  teams. With 3 teams the bottom-left cell is left empty, so every team stays in its own
  corner.

## Placement and the sides draw

- When the game is set up, the teams from the draw are given their squares. A coin flip
  decides which of the first two teams gets **Top Left** and which gets **Bottom Right**. The
  third team gets **Top Right**, and the fourth **Bottom Left**.
- The players already connected are **placed on their team automatically**, right after the
  map reset. The chat says so, then comes the frozen preparation time before GO. You start
  with the normal starting pack.
- If the setup has to retry, the coin flip is kept and a player already placed keeps their
  team.

## Arriving late

- A drawn player who connects late is **put on their own team automatically** when they join,
  even after GO, as long as that team is still in the game.
- Nobody else can join a team: players who were not drawn stay spectators.
- A player who never comes still counts as a no-show (see *Rating* below and the arrival
  rules in [How BB League works](how-it-works.md)).

## Feeding and waves

- **Science you send is split evenly across all the enemy teams still alive.** The chat and the
  science log show who received what.
- With 3 or 4 teams you can also **focus** part of your sends on chosen enemies (see
  *Science targeting* below).
- Each team is attacked by its own biters, from the area behind its square. Biters never cross
  the rivers.

## Science targeting (3 and 4 teams)

In a 3- or 4-team game, the **Feeding** panel has a **Focus** row with one button per enemy
team.

- **Any player of your team** can tick or untick an enemy team. The choice is shared by the
  whole team, and your team's chat says who changed it.
- When at least one enemy is ticked, **30 % of every science send** goes to the ticked teams
  (shared equally between them). **The other 70 % is spread over all enemy teams as usual**,
  ticked ones included.
- Example, 4 teams, one enemy ticked: that enemy receives 30 % + 70 %⁄3 ≈ 53 %, and the other
  two about 23 % each. With 3 teams and one enemy ticked: 65 % and 35 %.
- A button can't be clicked again for **60 seconds** after it was used.
- Only living enemy teams can be ticked. Nothing ticked means the plain even split.
- If a ticked team is eliminated, it stops receiving anything. If no ticked team is left alive,
  or only one enemy remains, sends go back to the plain even split.
- There is no Focus row in 2-team games (there is only one enemy), and none in training mode.
- It only works with the default **split** feeding policy. If an admin sets another policy,
  the Focus row disappears and clicks are refused.

## Elimination and results

- When a team's silo falls, the chat announces it **with its place** (for example
  "eliminated — 4th place"). Its players become spectators.
- The **last team standing wins**. The end screen lists every team's place.
- A team that has **nobody on it when the game starts** is closed: it gets no share of the
  sends and no biters, and the game is played by the other teams.
- A team that **empties during** the game is not spared: it keeps getting its share of every
  send and falls to its own biters.

## Rating

The game is rated on the **final places, 1st to last**, in one step (see
[BB League rating](rating.md)).

**A team that never showed up** (too few of its players connected) is taken out of the game:

- the teams that played are rated on their places among themselves;
- only the players who didn't show get the no-show penalty;
- if fewer than two teams really played, the game is not rated.

If the places among the teams that played can't be established (a tie or a missing place), the
game is not rated at all.

## Bets

On a 3- or 4-team game you bet on **which team finishes 1st**. Bettors on the winning team share
the pot. If the result can't be established, every bet is refunded.
