---
title: BB Bets API — programmatic trading
slug: bets-api
section: bets
order: 60
summary: REST + WebSocket access to the BB Bets engine for scripts and bots — tokens, endpoints, errors, rate limits, curl examples.
updated: 2026-09-14
icon: "🤖"
legacy_anchors: [sec-api]
---

REST + WebSocket access to the BB Bets engine for scripts and bots. Same fees, slippage caps
and balance checks as the web UI — no parallel implementation.

## Get a token

Visit [/science/api-tokens](https://biterbattles.org/index.php?r=science/api-tokens) (after
`askbot auth`) and click "Generate". Token shown **once**; cap 10 active per player.

```http
Authorization: Bearer bb_<64-hex>
```

Default scopes: `read,trade`. No `admin` scope yet (resolve/cancel stay web-only).

## Errors

Uniform envelope:

```json
{"error": {"code": "INSUFFICIENT_BALANCE", "message": "..."}}
```

Codes: `UNAUTHENTICATED`, `FORBIDDEN`, `INVALID_ARGUMENT`, `INSUFFICIENT_BALANCE`,
`INSUFFICIENT_SHARES`, `NOT_FOUND`, `MARKET_CLOSED`, `SLIPPAGE_EXCEEDED`, `OUT_OF_RANGE`,
`COOLDOWN`, `RATE_LIMITED`, `CANCEL_FORBIDDEN`, `METHOD_NOT_ALLOWED`, `TRADE_REJECTED`,
`INTERNAL`.

## Read endpoints (GET)

- `/api/v1/me` — auth check + balance.
- `/api/v1/balance` — earned/lost/locked breakdown.
- `/api/v1/markets` — list. Query: `status`, `category`, `limit`, `cursor`.
- `/api/v1/markets/{id}` — full detail (pools, prices, k, fees,
  outstanding_shares_per_option, my_shares, my_orders, my_lp).
- `/api/v1/markets/{id}/orders` — public order book (no player names).
- `/api/v1/orders` — your orders.
- `/api/v1/transactions` — your tx history.
- `/api/v1/pnl` — per-market PnL · `/api/v1/pnl/summary` — aggregate.
- `/api/v1/game-state` — live round snapshot: running, started_at, age, difficulty, players
  (online/active with freshness), open_winner_market.

## Trade endpoints (POST, JSON body, scope=trade)

- `/api/v1/buy` — `{market_id, option_index, coins, max_slippage_pct?}`
- `/api/v1/sell` — `{market_id, option_index, shares, max_slippage_pct?}`
- `/api/v1/orders/place` — `{market_id, side, option_index, price, amount}`
- `/api/v1/orders/cancel` — `{order_id}`
- `/api/v1/markets/create` —
  `{category, lp, options?, initial_prices?, title?, duration_minutes?, production_meta?}` ·
  1 market / 15 min cooldown

Optional `Idempotency-Key` header (any string ≤128 chars) — replays within 24h return the
cached response with the original status code. Rate limit: 60 req / 60 s per token
(HTTP 429 + `Retry-After`).

## WebSocket — events stream

```text
wss://biterbattles.org/bb-events
```

Token is sent as the first text message after connect (never appears in proxy logs):

```js
ws.send("auth bb_<your_token>");
// → {"kind":"auth_ok","player":"...","scopes":["read","trade"]}
// or  {"kind":"auth_error","reason":"..."}

ws.send("sub 0");      // firehose: every game / market / trade event
ws.send("sub 391");    // per-market: only events for market 391

// Event payload:
{"id":N, "kind":"new_trade", "market_id":391, "player":"X",
 "data":{...}, "at":"YYYY-MM-DD HH:MM:SS"}

ws.send("ping");       // → {"kind":"pong"}
ws.send("unsub 391");
```

Caps: 50 connections / IP, 10 / authenticated player, 20 subs / connection. Pre-auth idle
> 30s → close.

## Curl examples

```bash
TOKEN="bb_..."

# Buy 1c worth of option 0 (e.g. North) on market 391
curl -X POST -H "Authorization: Bearer $TOKEN" \
    -H "Content-Type: application/json" \
    -H "Idempotency-Key: $(uuidgen)" \
    -d '{"market_id":391,"option_index":0,"coins":1.0}' \
    https://biterbattles.org/api/v1/buy

# Get market detail
curl -H "Authorization: Bearer $TOKEN" \
    https://biterbattles.org/api/v1/markets/391

# Place + cancel a limit
curl -X POST -H "Authorization: Bearer $TOKEN" \
    -H "Content-Type: application/json" \
    -d '{"market_id":391,"side":"buy","option_index":0,"price":0.4,"amount":2}' \
    https://biterbattles.org/api/v1/orders/place

curl -X POST -H "Authorization: Bearer $TOKEN" \
    -H "Content-Type: application/json" \
    -d '{"order_id":47}' \
    https://biterbattles.org/api/v1/orders/cancel

# Create a custom market
curl -X POST -H "Authorization: Bearer $TOKEN" \
    -H "Content-Type: application/json" \
    -d '{"category":"custom","lp":5,"options":["Yes","No"],"title":"Will North win?","duration_minutes":60}' \
    https://biterbattles.org/api/v1/markets/create
```

Tokens leaked? Revoke from
[/science/api-tokens](https://biterbattles.org/index.php?r=science/api-tokens) — instant;
existing WS connections drop on next heartbeat.
