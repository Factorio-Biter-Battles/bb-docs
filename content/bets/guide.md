---
title: BB Bets trading guide
slug: bets-guide
section: bets
order: 30
summary: Shares, prices, the AMM and the order book, LP fees and the LVR maths, creating a market, resolution, and the full command reference.
updated: 2026-09-14
icon: "📊"
vars: [max_custom_duration_min]
---

## Core concepts — shares, prices, AMM, order book

A market has 2–5 options. Each option has **shares**. A winning share pays exactly **1c** at
resolution; a losing share pays **0c**. The live price of a share (in coins, between 0.001
and 0.999) is its *implied probability* — prices across options always sum to 1.

Two ways to get filled:

- **AMM (CPMM pool)** — buys push the price up, sells push it down. Early conviction pays
  off because later buyers pay more per share. Formula: constant-product `r_i × r_j = k`
  across option reserves.
- **Order book** — you can place limit buy/sell orders at a specific price. Market orders
  scan the book first and fill any limit priced better than the current AMM quote, then use
  the AMM for the remainder (**smart order routing**).

**Fees on every trade:** 1% is burned (permanently destroyed) + a **dynamic LP fee**
(base 3% + LVR compensation, scaling with how much your trade moves the price). A trade
that rebalances the market toward 50/50 pays only the 4% base; a trade that destabilizes it
pays more, up to a 71% cap. Preview every trade to see the exact breakdown. No house fee.
See the *LVR dynamic fees* section below for the math.

## Trading — market orders, limits, slippage

> ⚠ **In-game trading is disabled.** AMM fees (1% burn + dynamic 3–70% LP fee via LVR) make
> blind chat orders too risky. Trade on the website — it shows a fee + slippage preview
> before every confirm. Log in via `askbot auth` in-game chat (or
> `/spectator-chat askbot auth` during captain games) to get a token.

Market buy/sell (website only):

- Open a market page, type COINS → click the outcome button. Preview updates live on every
  keystroke with exact fee + shares + average price.
- Max-slippage field rolls back the trade if the price moves more than X% between preview
  and confirm.

Limit orders (website only):

- Click anywhere on the price chart to pre-fill the limit price, pick BUY/SELL + shares →
  confirm. Locked collateral shows in your "limits" panel.
- `askbot limit cancel OID` — still available in-game (cancellation has no fee risk).

Preview-only still works in-game: `askbot market preview buy|sell MID OPT AMOUNT` (no
execution, just tells you what a trade would cost).

**Option alias:** wherever OPT is expected you can use the index (`0,1,2…`), the full name,
or an unambiguous prefix (`yes`, `nor`, etc.).

**Why some trades are rejected as "too large":** a single AMM trade is capped at 50% of the
target option's pool reserve. This prevents one trade from pushing the price past extremes
(e.g. 99%→1%) on a thin pool and discourages whale-bombing. If you hit this, either *split*
the trade into smaller chunks, or use a *limit order* (no cap). Bigger pools = bigger trades
possible — you can also LP to grow the pool yourself.

## Liquidity providers (LP) — earning fees and risk

**What LPs are:** a market is just a pool of option-shares. When you LP, you deposit coins
in exchange for shares of every option — the pool owns those shares. Traders buy/sell
against the pool. On every trade, a **dynamic LP fee** is added to the pool: a 3% flat base
plus an LVR component that scales with how much the trade destabilizes the market. The more
trading — and especially the more lopsided the market gets — the more you earn. See the
*LVR dynamic fees* section for details.

- `askbot lp add MID COINS` — adds `COINS` of liquidity at the current price, in exchange
  for LP tokens. On a skewed pool (not 50/50), you also get back "bonus shares" of the
  cheaper options so that your deposit matches the pool's current price mix.
- `askbot lp remove MID AMOUNT|all` — burns LP tokens and returns you a proportional share
  of every option's reserve (you receive raw *shares*, not coins). You can sell those shares
  back to the pool to realize coins (as long as others are still LPing — see below).

  **Anti-rug-pull:** if other traders hold shares on the market, you can't remove LP past
  the point where the smallest pool reserve would drop below **25% of the largest
  outstanding non-LP share position**. The error tells you the maximum amount you can safely
  remove right now. Rationale: a solo LP pulling out completely would leave shareholders
  stuck with a dead AMM. Remove partial, wait for traders to rebalance, or wait for
  resolution.

**"Why do I get shares back? Who am I selling to?"** The pool literally holds shares of
every option. Your LP tokens are a claim on a slice of those shares. When you remove, the
pool hands you your slice. You then sell those shares back to the pool (same AMM everyone
uses). In effect you're selling to the *other LPs still in the pool* — they absorb your
shares and you get coins from the pool's collateral.

**You can't drain the last drop if others hold shares.** If you try to LP-remove while other
players still hold shares of this market, the system leaves a minimum 0.01c in the pool so
the AMM stays alive for them. Otherwise you'd strand their positions — they wouldn't be able
to sell until resolve.

**Impermanent loss:** if the price moves away from where you entered, withdrawing before
resolution can lock in a loss. Holding through resolution pays you the market-settled value
of the reserves (collateral minus payouts to winners).

**Leaving before resolve = forfeiting future fees.** LP fees accumulated since your last
action stay in the pool as collateral. At resolve, the surplus (collateral − winner payouts)
is distributed pro-rata to the *active* LP tokens. If you removed fully before resolve, you
took your shares with you but left the fee residue behind — others collect it, or if nobody
is left, it burns.

**LVR makes LPing profitable in expectation.** Before LVR, LPs were structurally losing to
informed traders (classic impermanent loss from directional markets). With dynamic fees at
α=3.0 plus a 3% flat base, the system over-compensates the pool's mark-to-market loss on
every trade — LPs net a small positive on average, even on markets that resolve decisively.
Chop markets (oscillating prices) are particularly lucrative for LPs because every swing
pays.

## LVR dynamic fees (the math)

The LP fee on every AMM trade is **dynamic**, based on how much the trade moves the pool's
mark-to-market value. The fee is sized to compensate liquidity providers for the
instantaneous loss a price-moving trade inflicts on the pool.

**Pool fair value** for an n-option CPMM with reserves `r_i`, invariant `k = Π r_i`, and
prices `p_i = (1/r_i) / Σ(1/r_j)`:

```text
V(p̄) = n · (k · Π_i p_i)^(1/n)
```

V is maximized at uniform prices (p_i = 1/n) and drops as the market skews toward extremes.
A pure swap preserves k but shifts prices, so V decreases — that's the LP's instantaneous
loss, called LVR.

**Per-trade LVR:**

```text
LVR = max(0, V(p̄_before) − V(p̄_after))
```

Zero if the trade rebalances (V increases), strictly positive if it destabilizes
(V decreases).

**Your fee:**

```text
fee_LP = min(0.03 · notional + 3.0 · LVR, 0.70 · notional)
```

Plus a flat 1% burn on top.

α = 3.0 means LPs over-compensate by 200% on LVR → systematically profitable on informed
flow. The 70% cap protects against degenerate fees on extreme whale trades. This was tuned
after observing historical LP losses of ~41% on the first wave of markets — the current
calibration is targeted at making LPs slightly positive on average across a variety of
resolution scenarios.

**Rule of thumb:** trades that move the market toward 50/50 pay only the 4% base (1% burn +
3% LP). Trades that push it further from 50/50 pay progressively more — up to 71% total on
the most destabilizing moves. Use the preview to see the exact breakdown before you commit.

**Limit orders are not LVR-fee'd.** P2P fills (limit orders matching) don't swap against the
AMM, so they pay a flat 4% (1% burn + 3% LP) regardless of price move. If you want to
market-make without paying LVR fees, use limit orders.

## Creating a market

Three types:

- **Winner** — auto-resolves at end of the current game to the winning team. Default
  options: `north`, `south`. Auto-created if none exists.

  `askbot market create winner LP [P_NORTH,P_SOUTH]`
- **Duration** — auto-resolves at end of the current game based on game length, with 5
  buckets: `<15m`, `15–30m`, `30–60m`, `60–120m`, `120m+`.

  `askbot market create duration LP [P1,P2,P3,P4,P5]`

  **Timer reference:** the duration measured is the *in-game match timer shown top-left*
  (match start → victory). It is **not** server uptime and **not** the time since the market
  was created — a market opened at minute 10 of a game that ends at minute 25 resolves on
  `15–30m`, not on `<15m`. Buckets are left-inclusive / right-exclusive (exactly 15m →
  `15–30m`, exactly 30m → `30–60m`, etc.).
- **Custom** — any question, 2–5 options, manual admin resolution (or auto-expire/cancel
  after DURATION_MIN minutes).

  `askbot market create custom TITLE:OPT1:OPT2[:OPT3:…]:DURATION_MIN:LP[:P1,P2,…]`

  TITLE cannot contain `:`. LP ≥ 5c. Prices are optional; if omitted, uniform.
  **DURATION_MIN: 1 to {{max_custom_duration_min}} min (max 4 weeks)** — long enough to span
  a full tournament.
- **Production** — *auto-resolving* bet on which team *produces* more — or, conversely,
  *loses* more — of a given item (lasers, walls, green chips, belts, etc.). Options are
  always `North` vs `South`. Resolves automatically based on live in-game stats either at
  **end of game** or at a chosen **game-time minute T**. Ties → market cancelled and
  refunded.

  "Produced" counts anything crafted (works for raw materials, intermediates, structures,
  fluids, science packs). "Lost" counts placed entities killed by enemies (walls, turrets,
  belts, roboports, …) — only placed items can be used in Lost mode.

  Create via the form at the top of the BB Bets page (not askbot — the item picker /
  direction / mode selection are safer to set graphically). Requires 1d+ playtime. Max 2
  open production markets per creator.

**Picking initial prices:** your opening pool determines where the market starts. If you
think YES is 70% likely, open at `0.3,0.7` rather than 50/50 — you'll pay less if you're
right and more if you're wrong. Extremes `0/100` are clamped to `0.001/0.999`.

**LP risk:** your 5c+ deposit is the market's liquidity. You earn trade fees (dynamic,
heavier on destabilizing trades) but you're also exposed to *impermanent loss* — if prices
move heavily and then resolve at an extreme, the pool is drained of the winning option. The
LVR compensation built into the fee formula (3% base + α=3.0 × LVR, capped at 70%) is
designed to over-compensate this IL on average, but any single market can still go against
the LP. Don't over-concentrate your LP on thin or highly directional markets.

## Viewing — shares, orders, balance, burns

- `askbot market list` — all open markets with current prices.
- `askbot market info MID` — full detail of one market.
- `askbot market orders MID` — top of book for a market.
- `askbot shares [PLAYER?]` (alias: `positions`) — your open positions (or another
  player's), mark-to-market value, unrealized PnL.
- `askbot myorders` — your open limit orders.
- `askbot coins [PLAYER?]` — your balance (or another player's).
- `askbot coins tip PLAYER AMOUNT` (alias: `send`) — tip another player (min 0.1c).
- `askbot pnl [PLAYER?]` — lifetime earned/lost (you or another player).
- `askbot burned` — total coins burned (24h and all-time).
- `askbot auth` — one-time token to log into the website.

## Resolution & cancellation

**Auto-resolve:** winner and duration markets resolve automatically when the game ends.
Winning shares each pay 1c; losing shares pay 0c. The pool's remaining collateral is
distributed pro-rata to LPs. Any leftover due to float drift or skewed initial prices is
burned (logged).

**Manual resolve (custom only):** `askbot market resolve MID OPT` — admin only.

**Cancel:** `askbot market cancel MID` — creator can cancel only if there are no trades yet;
admin can cancel any time. Cancel refunds: net P&L to each trader, original deposit to each
LP, and unfilled collateral to each open order.

## Admin commands

- `askbot market resolve MID OPT` — force-resolve a custom market.
- `askbot market cancel MID` — cancel any market.

Admin is the server admin list (case-insensitive).

## FAQ

### Can I lose more than I put in?

No. Each share is worth 0–1c; the worst-case value of your position is 0. LP deposits cap
your LP-side loss at the deposited amount minus fees earned.

### Why is my limit order not filling instantly?

Limits only fill when someone sends a market order at a price that crosses yours, or when
the AMM price is pushed past your limit. Watch the book via `askbot market orders MID`.

### Can I remove liquidity before resolution?

Yes. You get proportional shares back (not coins); sell them to convert.

### What happens to burned coins?

They're permanently gone from the supply — the burn is the game's deflationary sink against
inflation from kills/challenges.

### What about slippage?

Optional cap on buy/sell. If price moves more than X% during the fill (e.g. because a limit
order filled first or the pool is thin), the transaction rolls back and you keep your coins.

### Why are my fees not immediately visible?

LP fees (base 3% + LVR component) stay inside the pool and grow reserves — you realize them
when you withdraw LP or at resolution. The 1% burn is logged in the burn feed. Your current
LP position's mark-to-market value accounts for accumulated fees in real time.

### How are duplicate bucket/option names handled?

Rejected at creation — option names must be unique case-insensitively, 1–64 chars.

## Complete command reference

| Command | Does |
| --- | --- |
| `askbot market help [trade\|lp\|create\|info\|admin]` | Help, paginated by section. |
| `askbot market list` | Open markets + current prices. |
| `askbot market info MID` | Detail of one market. |
| `askbot market orders MID` | Order book. |
| `askbot market preview buy\|sell MID OPT AMOUNT` | Dry-run a trade. |
| `askbot market create winner LP [P1,P2]` | Auto-resolving winner market. |
| `askbot market create duration LP [P1..P5]` | Auto-resolving duration market. |
| `askbot market create custom TITLE:OPT1:…:DURATION:LP[:PRICES]` | Manual-resolve market. |
| `askbot market resolve MID OPT` | Admin: force resolve. |
| `askbot market cancel MID` | Creator (no trades) or admin. |
| `askbot buy MID OPT COINS [SLIP%]` | Market buy. |
| `askbot sell MID OPT SHARES\|all [SLIP%]` | Market sell. |
| `askbot limit buy\|sell MID OPT PRICE SHARES` | Place limit order. |
| `askbot limit cancel OID` | Cancel limit order. |
| `askbot lp add MID COINS` | Add liquidity. |
| `askbot lp remove MID AMOUNT\|all` | Burn LP tokens for shares. |
| `askbot shares [PLAYER?]` / `askbot positions [PLAYER?]` | Your (or another player's) open positions. |
| `askbot myorders` | Your open limit orders. |
| `askbot coins [PLAYER?]` | Your (or another player's) balance. |
| `askbot coins tip PLAYER AMOUNT` (alias: `send`) | Tip another player (min 0.1c). |
| `askbot pnl [PLAYER?]` | Lifetime PnL. |
| `askbot burned` | Total burn stats. |
| `askbot auth` | Get a one-time login token for the website. |
| `askbot borrow LENDER AMOUNT DAYS` | Request a loan from another player. |
| `askbot loan confirm ID` | (Lender) approve a pending loan — coins transfer. |
| `askbot loan deny ID` | (Lender) decline a pending loan. |
| `askbot loan cancel ID` | (Borrower) cancel your own pending request. |
| `askbot loan repay [ID]` | Repay your active/defaulted loan in full. |
| `askbot loan status [PLAYER?]` | Show recent loans for you (or another player). |
| `askbot loan help` | Short loans cheat sheet. |

Loans have their own page: [borrowing and lending coins](loans.md).
