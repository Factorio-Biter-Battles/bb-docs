---
title: BB Bets — prediction markets
slug: bets
section: bets
order: 20
summary: Polymarket-style binary and multi-outcome markets paying 1c per winning share — the tools, the mechanics, the in-game commands.
updated: 2026-09-14
icon: "📈"
legacy_anchors: [sec-bets]
---

Polymarket-style binary & multi-outcome markets paying 1c per winning share. Mid-price =
market-implied probability. Fully on-chain (BB Coins).

## Live tools

- [Markets dashboard](https://biterbattles.org/index.php?r=science/bb-bets) — list of all
  live markets, prices, volume.
- [Create a market](https://biterbattles.org/index.php?r=science/bb-market-create) —
  winner / duration / production / custom (1d+ playtime, max 2 concurrent custom).
- Per-market detail page: live order book, trades timeline, holders table, your
  positions / PnL, fee + slippage preview before every trade. Click on the chart to set a
  limit price.
- Admins logged in via `askbot auth` can resolve / cancel any market directly from its
  page.

## Mechanics

- **AMM** — constant-product reserves, share prices = pool ratios. Buy with coins, sell
  shares back, get instant fills.
- **Limit orders** — placed at any price ∈ (0, 1c). Filled peer-to-peer when AMM crosses or
  another taker matches.
- **LP** — add coins to a market's pool, earn 3% base + 3.0×LVR dynamic fee on each trade
  (capped at 70% of trade size — over-compensates impermanent loss). Withdraw anytime.
- **Fees** — 1% burn on every AMM trade + dynamic LP fee. Limit fills pay flat 4%. Always
  preview before clicking buy/sell.
- **Resolution** — winner/duration auto-resolve at game end, production at game end or
  T-minute, custom is creator/admin-resolved.

The [trading guide](guide.md) explains all of this in detail, including the fee maths.

## In-game commands (read-only / non-fee)

- `askbot market list` · `info <mid>` · `orders <mid>` · `shares` · `myorders` · `burned`
- `askbot market preview buy|sell <mid> <opt> <amt>` — fee breakdown
- `askbot market create winner|duration|custom …` · `cancel <mid>`
- `askbot market lp add|remove <mid> <amt|all>`
- **Buy/sell/limit are web-only** — type `askbot auth` first, then trade with fee preview.
- `askbot market help trade|lp|fees|create|info|admin` — section-specific help.
