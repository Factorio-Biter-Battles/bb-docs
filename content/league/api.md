---
title: BB League API — matches, Elo, live, gamble
slug: league-api
section: league
order: 40
summary: Read-only JSON access to the BB League — match list and full match detail (production, per-player, splits, minute history, Elo), leaderboard, player card, live match, coin gamble.
updated: 2026-09-16
icon: "🤖"
legacy_anchors: [sec-league-api]
---

Read-only JSON access to the **BB League** — the permanent 1v1…8v8 ladder that runs on the
BB League server. Every match page, the Elo ladder, the live match and the coin gamble are
exposed here. Six endpoints, all `GET`, all authenticated with the same token as the
[BB Bets API](../bets/api.md).

Base URL: `https://biterbattles.org/api/v1/league`

---

## 1. Authentication

Identical to the BB Bets API.

1. In Factorio chat, type `askbot auth` (or `/spectator-chat askbot auth` during a captain
   game). The bot whispers an 8-character, 10-minute, one-shot code.
2. Open [the BB Bets page](https://biterbattles.org/index.php?r=science/bb-bets), paste your in-game name + the code.
3. Generate a token on [the API tokens page](https://biterbattles.org/index.php?r=science/api-tokens). It is shown **once**;
   10 active tokens per player maximum.

```text
Authorization: Bearer bb_<64 hex>
```

All six endpoints need the `read` scope only — nothing here can write, spend or move anything.

**Rate limit**: 600 requests / 60 s per token (shared with the BB Bets read endpoints).
Over the limit: HTTP 429 with a `Retry-After` header.

**Errors** use the API's uniform envelope:

```json
{"error": {"code": "NOT_FOUND", "message": "League match 4242 not found"}}
```

| Code | HTTP | When |
|---|---|---|
| `UNAUTHENTICATED` | 401 | missing / malformed / revoked token |
| `FORBIDDEN` | 403 | token lacks the `read` scope |
| `INVALID_ARGUMENT` | 400 | bad `status`, `format`, `since`, or an array-shaped parameter |
| `OUT_OF_RANGE` | 400 | `player` longer than 64 characters |
| `NOT_FOUND` | 404 | unknown match / player / gamble box, or a **Cup** match id |
| `RATE_LIMITED` | 429 | 600/min exceeded |

---

## 2. Conventions

- **Times are UTC**, unlabelled, `YYYY-MM-DD HH:MM:SS` — the format the database stores.
- **Coins are integer cents** in every `*_cents` field. The same amount is repeated as a
  float (`total_a`, `amount`, `payout`, …) for convenience. Cents are the truth; the float
  is rounded to 2 decimals.
- **A League match is not a Cup match.** The League lives in its own tournament round
  (`tt_round.idx >= 95`, label "Ladder"). Cup and BB Masters matches are *not* served by
  these endpoints and a Cup match id returns `404 NOT_FOUND`.
- **Rosters vs sides.** A match has two rosters, `a` and `b`. Which one spawns *north* is
  drawn at match prep, so `a` is **not** north:
  - `side_of_a` — `"north"`, `"south"`, or `null` if the draw is not known yet;
  - `sides` — `{"north": [...], "south": [...]}`, or `null` before ingest;
  - `winner` — `{"side": "north"|"south"|null, "team": "a"|"b"|null, "players": [...]|null}`.
- **Voided games.** A referee cancel / emergency nuke writes `status: "forfeit"` with
  `score: "cancelled"`; the item carries `voided: true`, no winner and no Elo. Read it as
  "this game did not happen", not as a forfeit win.
- **`format`** is `count(roster.a) + "v" + count(roster.b)` — `"1v1"` … `"8v8"`.

---

## 3. `GET /api/v1/league/matches`

League matches, newest first, cursor-paginated.

| Param | Default | Notes |
|---|---|---|
| `status` | `all` | `scheduled`, `live`, `done`, `forfeit`, `bye`, `all` |
| `player` | – | exact player name, case-insensitive, matched in either roster (≤64 chars) |
| `format` | – | `1v1` … `9v9` |
| `since` | – | `YYYY-MM-DD` or `YYYY-MM-DD HH:MM[:SS]`, UTC, filters on `created_at` |
| `limit` | `25` | max 100 |
| `cursor` | – | a match id; returns matches with a **smaller** id (older) |

```json
{
  "matches": [
    {
      "id": 3152,
      "status": "done",
      "voided": false,
      "format": "1v1",
      "score": "1-0",
      "server": "sandbox",
      "created_at": "2026-09-15 20:45:29",
      "scheduled_at": "2026-09-15 20:45:34",
      "duration_min": 31,
      "roster": {"a": ["neuro666"], "b": ["joschua725"]},
      "side_of_a": "north",
      "sides": {"north": ["neuro666"], "south": ["joschua725"]},
      "winner": {"side": "south", "team": "b", "players": ["joschua725"]},
      "elo": {"joschua725": 12.4, "neuro666": -12.4},
      "gamble": {
        "status": "settled", "side_a": "north",
        "total_a_cents": 200, "total_b_cents": 700,
        "total_a": 2, "total_b": 7,
        "n_bets": 5, "winner_side": "b"
      },
      "url": "https://biterbattles.org/index.php?r=science%2Ftt-game&id=3152"
    }
  ],
  "next_cursor": 3152
}
```

`elo` is a map *player → rating delta* (empty `{}` when the match was not rated: wrong roster
sizes, voided game, still running). `gamble` is `null` when the match never had a betting box.
`next_cursor` is `null` on the last page — pass it back as `cursor` otherwise.

---

## 4. `GET /api/v1/league/matches/{id}`

Everything the public match page renders. Returns every field of the list item above (with
`gamble` upgraded to the full block of §8), plus:

| Field | Content |
|---|---|
| `round` | `{id, idx, label, phase}` — always the Ladder round |
| `team_a`, `team_b`, `winner_team_id` | the internal placeholder team ids; **the team names mean nothing**, name the sides from the rosters |
| `market_id` | BB Bets market opened on this match, if any |
| `chart_game_id` | the `bb_live_stats_history.game_id` of the series below (`900000000 + id`) |
| `summary` | `{evo:{north,south}, threat:{north,south}, victory_time, difficulty, duration_ticks, online_at_end, ingested_at}` |
| `team_stats` | per side (`north`/`south`): `items`, `fluids`, `placed`, `sci`, `milestones`, `tech_count`, `ent_built_tot`, `ent_mined_tot` — the end-of-game production mirror |
| `per_player` | `[{name, team, built, mined, kills, deaths}]`, best builder first |
| `splits` | speedrun splits per side: `{split_key: elapsed_ticks}` (`sci_auto`, `sci_log`, `sci_mil`, `ms_electronics`, …) |
| `history` | the per-minute series the charts draw, oldest first, **max 600 rows** |
| `history_truncated` | `true` when the 600-row cap was hit |
| `final_pred_north_pct` | last non-null win prediction of the series |
| `elo_log` | `[{player, rating_before, delta, rating_after, created_at}]` — the engine's own settlement log |
| `paid_at`, `live_game_id` | bookkeeping |

Each `history` row: `ts` (unix), then per side (`n_` = north, `s_` = south)
`laser nuke robo pu lds rf cbot iron`, the seven `sci_*` pack counters,
`pow_cap_w` / `pow_cons_w`, `built_tot` / `mined_tot`, and the shared `pred_north_pct`.
Power, totals and the prediction can be `null`; everything else is an integer.

A match that was never ingested (a forfeit before the first tick) answers with `summary`,
`team_stats` and `gamble` at `null` and empty `per_player` / `history` — it is still a 200.

---

## 5. `GET /api/v1/league/leaderboard`

The site's ladder, in the site's exact order: the **ranked** block first
(`games >= min_games`), then the placement block; inside each block
`rating DESC, games DESC, player ASC`. `rank` is absolute, so page 2 continues page 1.

| Param | Default | Max |
|---|---|---|
| `limit` | 50 | 200 |
| `offset` | 0 | – |

```json
{
  "min_games": 5,
  "total": 34,
  "ranked_total": 17,
  "limit": 5, "offset": 0,
  "players": [
    {"rank":1,"player":"Carl3","rating":1161.67,"games":20,"wins":16,"losses":3,
     "ranked":true,"updated_at":"2026-09-14 21:26:32"}
  ]
}
```

Ratings start at 1000 and the population mean stays pinned there (the engine is zero-sum),
so "above 1000" always means "above average". A 1v1 moves half as much as a team game.

---

## 6. `GET /api/v1/league/players/{name}`

```json
{
  "player": "UwUmeowFables",
  "rating": {"rating":1056.43,"games":11,"wins":8,"losses":3,
             "ranked":true,"min_games":5,"updated_at":"2026-09-15 22:06:54"},
  "record": {"games":11,"wins":8,"losses":3},
  "matches": [ /* the last 20, same shape as §3 */ ]
}
```

The lookup is case-insensitive. `rating` is `null` for a player who has League matches but no
rating row yet. `404 NOT_FOUND` when the name has neither. URL-encode a name that contains a
space or a reserved character (`/api/v1/league/players/foo%20bar`).

---

## 7. `GET /api/v1/league/live`

Passthrough of the live snapshot the result watcher rewrites every 15 s.

```json
{"state":"none","next":[],"ts":1789512391,"age_sec":18}
```

| `state` | Meaning |
|---|---|
| `none` | no match running |
| `live` | a match is running — the payload then also carries `match`, `sides`, `elapsed_ticks`, `evo`, `threat`, `online`, `players`, `team_stats` |
| `stale` | the snapshot says `live` but is older than 90 s — treat the numbers as suspect |
| `unknown` | the snapshot could not be read at all |

`age_sec` is added by the API. Poll at most every 15 s; there is nothing newer to see.

---

## 8. `GET /api/v1/league/gamble/{match_id}`

The coin gamble on one match. A box opens when the sides are drawn and freezes at GO;
settlement is **parimutuel with no house**: winners take their stake back and split the losing
pot pro rata (integer cents, remainder to the largest stake, fee 0%). A single-sided pot, a
cancel, a forfeit, a nuke or a no-show refunds everyone in full.

```json
{
  "match_id": 3152,
  "box": {"match_id":3152,"status":"settled","side_a":"north",
          "total_a_cents":200,"total_b_cents":700,"total_a":2,"total_b":7,
          "n_bets":5,"winner_side":"b","fee_cents":0,
          "opened_at":"2026-09-15 20:52:41","frozen_at":"2026-09-15 20:56:58",
          "settled_at":"2026-09-15 21:27:45"},
  "bets": [
    {"id":43,"player":"UwUmeowFables","side":"b","amount_cents":500,"amount":5,
     "placed_at":"2026-09-15 20:54:26","status":"won",
     "payout_cents":644,"payout":6.44,"settled_at":"2026-09-15 21:27:45"}
  ],
  "totals": {"pot_cents":900,"pot":9,"total_a_cents":200,"total_b_cents":700,
             "n_bets":5,"n_bets_listed":5,"paid_out_cents":900,"paid_out":9,"fee_cents":0}
}
```

`box.status` is `open` | `frozen` | `settled` | `refunded`; `winner_side` and each bet's
`status` (`open`/`won`/`lost`/`refunded`) are `a`/`b` — **roster** sides, not north/south; map
them through `side_a`. Bet lists are capped at 500 rows (`n_bets` vs `n_bets_listed`).
Punter names are public: the in-game BET panel shows them to everyone on the server.

`404 NOT_FOUND` when the match never had a box — the kill switch was off, the same two
line-ups had already played within the 90-minute pair cooldown, or it is not a League match.

---

## 9. Recipes

```bash
TOKEN="bb_..."
API=https://biterbattles.org/api/v1/league

# The last 3 matches, with pots and Elo deltas
curl -H "Authorization: Bearer $TOKEN" "$API/matches?limit=3"

# One player's 1v1 record this month
curl -H "Authorization: Bearer $TOKEN" \
     "$API/matches?player=Carl3&format=1v1&status=done&since=2026-09-01"

# Walk the whole history, 100 at a time
cursor=""; while :; do
  page=$(curl -s -H "Authorization: Bearer $TOKEN" "$API/matches?limit=100&cursor=$cursor")
  echo "$page" | jq -c '.matches[] | {id,format,status}'
  cursor=$(echo "$page" | jq -r '.next_cursor // empty'); [ -z "$cursor" ] && break
done

# Production of both sides on one match
curl -s -H "Authorization: Bearer $TOKEN" "$API/matches/3149" | jq '.team_stats'

# Who is winning right now
curl -s -H "Authorization: Bearer $TOKEN" "$API/live" | jq '{state, evo, elapsed_ticks}'
```
