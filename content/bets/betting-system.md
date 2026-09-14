---
title: BB Bets — how the betting system actually works
slug: betting-system
section: bets
order: 50
summary: The full walkthrough of BB Coins and the prediction market — what it does today, how it got here, and where we know it is weak.
updated: 2026-09-14
icon: "🧾"
redirect_from: [bb-betting-system]
---

*A full walkthrough of BB Coins and the BB Bets prediction market: what it does today, how it
got here, and where we know it is weak.*

This document exists because a player sent us a sharp, well-argued critique of the market
design. Rather than answer it in three Discord lines, we wrote down the whole picture — the
history, the numbers, the design decisions and the mistakes — so the discussion can continue on
solid ground. Some of the critique is right, and we say so plainly below.

---

## 1. BB Coins in one page

BB Coins (`c`) are a **game-only** currency. They cannot be bought, sold or cashed out for
anything. They exist to make watching and playing Biter Battles more interesting.

**How you get them**

| Source | Roughly |
|---|---|
| Welcome bonus | 10c, once, when you first reach 1 day of tracked playtime |
| Winning a normal game | scaled by the time you actually played that round (base 1c per 4h of play, currently doubled) |
| Winning a captain game | same base, with a larger multiplier for captains |
| MVP vote | up to 3c / 2c / 1c to the top three voted players on each team, when the vote reaches quorum |
| 3v3 tournament win | flat 10c per winning player who actually played (30c per match) |
| Tips | any player can send coins to another player |
| Trading | profits from BB Bets |
| Loans | a peer-to-peer lending feature exists, with garnishment on default |

You need **1 day of tracked playtime** before you have an account at all. That is the anti-alt
gate: no fresh account can appear, bet, and vanish.

**How they disappear**

Every coin that enters the economy is inflation, and inflation makes prices meaningless. So
there are two sinks:

- **Trade fees.** 1% of every trade is *burned* — destroyed, not paid to anyone.
- **Scheduled decay.** A weekly job burns coins from **inactive** accounts (positive balance,
  under 1h of play in the last 7 days) to offset recent emission. The economy targets a
  **market cap of 10,000c**. Below target it burns 50% of the week's net inflation (slow the
  growth, don't freeze it); at or above target it burns 150% (push back down). The burn is
  spread across inactive accounts in proportion to balance. Nobody can go negative, and active
  players are never touched.

At the time of writing the market cap sits at roughly **9,870c across ~965 accounts** — close
to target, by design.

---

## 2. Timeline: how we got to the current system

The betting system is not a design that was drawn once and shipped. It is about fifteen
iterations, most of them forced by something breaking or a player finding a hole. Here is the
honest version.

### v1 — parimutuel pools (14–17 April 2026)

The original idea was simple and explicitly Polymarket-flavoured: anyone with 1d+ playtime can
open a bet on the current game (who wins, how long it lasts), other players stake coins on
outcomes, and at game end the pot is split among the winners. The market creator took a 1%
commission — taken from the pot, not minted.

What v1 got right: it was trivially understandable, and the automatic oracle (the game itself
tells us who won and how long it took) meant no human had to arbitrate.

What went wrong, fast:

- **Odds didn't move.** In a parimutuel pool you find out your real payout only at the end.
  There was no live price, so nothing to react to, and no reason to bet early rather than late.
- A player put it precisely: *"Need to incentivize betting early instead of late. With some
  mechanic that gives betting early an advantage over late to compensate for the amount of
  info gained betting late."* That single message is what killed v1.
- Hedging was free-ish. Multiple bets per market were allowed for a while (players asked for
  it), which made "put a bit on both sides" a way to park coins with only the 1% commission as
  friction. v1 was later locked down to **one bet per market per player** — which is where the
  single-side idea first appears, four months before it came back in v2.

v1 still exists in the codebase as a frozen legacy path. Nothing trades on it.

### v2 — the CPMM (17 April 2026)

v2 replaced the pool split with a real **constant-product market maker** plus an order book:

- Prices are live and move with every trade, so early information is rewarded immediately.
- Anyone can create a market by depositing **at least 5c** of liquidity and choosing the
  starting prices. That deposit makes them the first liquidity provider (LP).
- Fees at launch: **1% burned + 1% to LPs**, no house cut.
- 2 to 5 options per market; a 5-bucket duration market (`<15m`, `15-30m`, `30-60m`,
  `60-120m`, `120m+`); custom markets with a title and a deadline.
- All v1 trading was killed and affected players refunded.

Within 48 hours the first structural problem showed up, and it is exactly the one the critique
is about.

### The LP problem (17–19 April 2026)

**Symptom:** market creators kept losing money. On one early market the creator seeded the pool,
one side ran away, and the creator ended up holding the losing bag. On another, an LP put in 5c
and got 2.4c back.

**Diagnosis (still true today):** an AMM liquidity provider is the automatic counterparty to
everyone who is right. If a market gets one-sided informed flow and little else, the LP simply
pays that trader. Fees only compensate when there is *two-way* volume to earn them on. In an
April post-mortem we wrote: *"1 informed bettor → maximum directional adverse selection. No
mean-reversion: you need two camps arguing for the LP to scalp the oscillations."*

The owner's position was blunt and has not changed since: *"I want a system where LPs win most
of the time, at the expense of traders — they're the ones who should be eating well."*

**Fixes, in order:**

1. **Dynamic, loss-aware LP fee (18 April).** Instead of a flat percentage, the LP fee on an
   AMM swap is computed from *how much that specific trade moves the price against the pool*.
   A trade that barely nudges the odds is cheap; a trade that violently re-rates the market
   pays a lot. The overshoot coefficient started at 1.5 (deliberately more than break-even, so
   LPs come out ahead on average), with a hard ceiling so a single trade can never be eaten
   alive by fees.
2. **Retuned twice on real data.** After analysing the eight largest markets to date, the
   coefficient went 1.5 → 2.5 → **3.0**, the base LP fee 1% → **3%**, and the ceiling
   55% → **70%** of trade notional. These are the values running today.
3. **Fee preview everywhere (18 April).** Because a variable fee that you discover after the
   fact is user-hostile, the buy/sell preview on the site and API now shows exactly what the
   trade will cost before you confirm.

### The LP dilution bug (18 April 2026)

A player did the accounting on his own position and reported it: *"I held 100% of the LP for
64% of the volume … at resolve my LP paid was 2.426c out of 5c contributed. Shouldn't that be
more?"*

He was right. New LPs joining a market were being credited against a stale token counter that
did not account for fees already accumulated in the pool — so a late joiner claimed a slice of
fees earned before they arrived, diluting everyone who had been there. Fixed by recomputing
the LP share from the live pool state at every deposit. Affected players were refunded.

### Locking LP withdrawals (19 April 2026)

Once fees became substantial, a new pattern appeared: park liquidity, collect fees during the
opening burst, then pull out just before resolution to dodge the loss — leaving whoever stayed
holding the bag. There was also a plain UX disaster: an LP could withdraw and leave a live
market with almost no depth, so nobody could sell.

Two guards went in:

- A **minimum-depth rule** on withdrawal (after your withdrawal, the thinnest option must still
  hold at least 25% of the largest outstanding position, so shareholders can always exit).
- Then, as a stronger temporary measure that is **still active today**: **LP withdrawals are
  disabled while a market is open.** Your liquidity and your share of the pool are returned
  automatically at resolution, or refunded if the market is cancelled. A confirmation step
  warns you before you deposit.

### Production markets (18–19 April 2026)

A player suggested markets on in-game production stats. It was a good idea because the oracle
is automatic — the game knows how many green circuits each side made. Shipped as a market
category: pick an item from a fixed list (37 items and fluids), pick "produced" or "lost",
pick "at game end" or "at minute T", and the market resolves itself. "Lost" is restricted to
placed entities (walls, turrets, belts…), where losses are actually meaningful.

That feature also produced our most embarrassing bug: on a "gun turrets lost" market the
resolution counted the wrong statistic and announced 2,056 vs 21,247 when the in-game panel
showed 34,901 vs 54,957. It was reported by a player with a screenshot. The oracle now uses
exactly the same formula as the in-game Team Statistics panel.

### In-game trading disabled (20 April 2026)

With a variable fee, blind chat commands became genuinely dangerous — you could not see what a
trade would cost before sending it. In-game buy/sell/limit commands were removed. You still
authenticate from in-game, but the actual trading happens on the website or through the API,
where the preview is visible.

### Price vs cost (25 April 2026)

Recurring confusion, worth stating clearly here: a player reported *"you can buy shares when
they're beyond 1c, but you cannot place a sell limit at e.g. 1.1c per share."* Not a bug. See
§3 — a share pays exactly 1c if it wins, so no limit above 1c can ever be rational, while the
*effective cost per share you paid* (which includes fees and slippage) can absolutely exceed
1c. A red warning now appears in the preview when that happens.

### API, WebSockets, hardening (26–28 April 2026)

A REST + WebSocket API was added so players can build their own clients. This triggered several
rounds of adversarial review; a session cookie exposure and a handful of input-validation holes
were found and closed before anything was abused. Live price charts, an order book, a depth
chart, per-player P&L panels and a public transaction explorer all landed in this window.

### The single-side rule (7 June 2026, refined 15 June)

An audit turned up **15+ historical cases** of players holding *both* sides of the same binary
market. That combination is not a normal bet: holding both sides and then unwinding only one of
them back into the pool is a near risk-free hedge whose cost is paid entirely by the liquidity
providers. Left alone, a market can be drained this way while looking like ordinary volume.

The fix — a deliberate design decision, not an oversight:

> **One side per market.** On a binary market you may hold an open directional position on only
> one option at a time. Selling is never restricted, and re-buying the same side is always fine.

Two refinements followed within a week, both from player bug reports:

1. **Binary markets only.** The first version blocked *every* market. But spreading across 3+
   outcomes (e.g. the 5-bucket duration market) is perfectly legitimate. Multi-option markets
   are exempt.
2. **LP inventory doesn't count.** When you provide liquidity to a skewed pool you receive some
   shares back as inventory. That is not a bet, and it was wrongly tripping the guard on LPs.
   Those shares are now tracked separately: an LP holding North inventory can still bet South,
   and can dump the inventory whenever they like.

Existing both-side holders were grandfathered — the rule blocks new buys, never sales, so
everyone could unwind.

### Custom market duration cap (13 June 2026)

Custom markets could technically be opened for 90 days. Capped to **4 weeks (40,320 minutes)**,
and decoupled internally from the far-future placeholder used by the auto-resolving game
markets.

### 3v3 tournament markets (13 July 2026 → today)

The Summer 26 BB Cup (3v3 tournament) introduced house-created markets, live betting during matches,
and automatic settlement from the match result. Details in §6.

---

## 3. How the v2 AMM works today

### Shares and prices

A market has 2–5 **options**. Buying a share of an option costs less than 1c and pays exactly
**1c if that option wins, 0c otherwise**. So a price is directly a probability: "North at
0.62c" means the market thinks North wins 62% of the time.

Quoted prices live in **[0.001, 0.999]**. The reason is the 1c cap: nothing can be worth more
than certainty, and something at exactly 0 or 1 would break the pool math. A market's starting
prices and any limit order outside that band are rejected outright; the AMM's own price can
approach the edges but never reaches 0 or 1, because the pool invariant does not allow it.

### The pool

Liquidity sits in one reserve per option. The invariant is the classic constant product:

```text
k = r₀ × r₁ × … × rₙ₋₁          (kept constant by every swap)
price_i = (1/r_i) / Σ (1/r_j)   (a price is just the inverse-reserve share)
```

When a market is created with collateral `C` and starting prices `p`, the reserves are set to
`r_i = C × min(p) / p_i`. For a 50/50 binary market seeded with 20c, that is simply
`r = [20, 20]`, `k = 400`.

Buying option *i* with `x` coins of collateral works like this: `x` is added to **every**
reserve (you are minting a complete set of shares), then reserve *i* is reduced back to the
level that keeps `k` constant. The difference is the shares you receive. Selling is the same
motion in reverse.

### Worked example (real numbers from a real market)

Round 2 of the 3v3 tournament, Busbiters vs Chua. Market seeded by the house with 20c at 50/50,
so `r = [20, 20]`, `k = 400`.

The first bettor buys **2c of Chua**:

| Step | Value |
|---|---|
| Burn fee (1%) | 0.020c → destroyed |
| LP fee (base 3% + loss-compensation add-on) | ≈ 0.263c → added to the pool |
| Collateral actually swapped | ≈ 1.717c |
| Reserves after the swap | ≈ [21.72, 18.42] |
| Reserves after the LP fee is added back | ≈ [21.98, 18.68] |
| **Shares received** | **3.298 Chua** |
| **Price before → after** | **50.00% → 54.06%** |
| Effective cost per share | 2 / 3.298 = **0.606c** |

Note the two things a new trader should take away. First, the price moved 4 points on a 2c
trade against a 20c pool — that is the *slippage*, and it is a function of pool depth. Second,
you paid 0.606c per share while the market only re-rated to 0.541 — the gap is the fee wedge,
and it is why the fee level matters so much to whether betting the favourite stays attractive.
Hold that thought for §7.

### Fees, in full

| Component | Rate | Goes to |
|---|---|---|
| Burn | **1%** of trade notional, always | destroyed (deflation) |
| LP base | **3%** of notional | the pool (all LPs, pro-rata) |
| LP loss-compensation | proportional to how far *this* trade moves the pool's mark-to-market value, multiplied by **3.0** | the pool |
| Ceiling | total LP fee never exceeds **70%** of notional | — |
| Peer-to-peer limit fills | flat **1% burn + 3% LP**, no add-on | — |

The loss-compensation term is the important one. A trade that nudges the odds is nearly free; a
trade that re-rates the market violently pays a large fee, because that is precisely the trade
that costs the liquidity providers money. It is calculated per trade, shown in the preview
before you confirm, and it is the reason the total fee on a big directional buy can look
surprisingly high.

### Other limits

- Minimum trade **0.1c** (dust guard).
- The collateral entering a single AMM trade (after fees) cannot exceed **50% of the target
  option's reserve**. Split it.
- Minimum liquidity to open a market: **5c**.
- A player may keep at most **2 open custom markets** and **2 open production markets** at once
  (the tournament house account is exempt).
- Optional max-slippage parameter on buys and sells: the trade reverts if the price moves more
  than you allowed.

### Limit orders

There is a real order book alongside the AMM. You can place a buy or sell limit at any price in
[0.001, 0.999]. Buys lock collateral; sells lock shares. A market buy is **smart-routed**: it
first eats any resting sell orders priced *below* the AMM price, then sends the remainder to
the AMM. Peer-to-peer fills pay the flat fee, with no loss-compensation add-on — matching
another player directly costs the pool nothing, so it is cheaper.

### Liquidity providers

The market creator is the first LP. Anyone can add liquidity to an open market afterwards
(minimum 5c). LP tokens are the geometric mean of the reserves, recomputed live, so a late
joiner cannot claim fees earned before they arrived.

At resolution, in this order: **winning shareholders are paid 1c per share first**, then LPs
split whatever collateral is left, pro-rata to their tokens. If the winning shares ever
exceeded the collateral (a bug condition, not a normal one), payouts are scaled down pro-rata
rather than minting coins from nothing. Any residual dust is burned.

As covered above, **LP withdrawals before resolution are currently disabled**.

### The house subsidy — stated openly

For house-created markets (3v3 matches), the liquidity comes from a funded bot account. This is
a **deliberate subsidy**: it is the price of having a market at all. Without someone posting the
first liquidity, there is nothing to trade against, and asking players to fund a market on a
match they want to bet on is a chicken-and-egg problem.

Whether the house actually *loses* that subsidy is an empirical question, so here are the real
numbers across the nine settled 3v3 markets to date (20c seeded each):

| Market | Returned to house | Net |
|---|---|---|
| Two early test markets, no trades | 20.00c each | ±0 |
| Practice A | 18.67c | −1.33c |
| Practice B | 23.33c | +3.33c |
| Round 1 — Steelaxe Mafia vs BiterBattleBus | 17.81c | −2.19c |
| Round 1 — Busbiters vs One and Two Half Men | 17.56c | −2.44c |
| Round 1 — MCP vs Chua | 21.59c | +1.59c |
| Round 2 — Busbiters vs Chua | 35.67c | **+15.67c** |
| One cancelled fixture | 20.00c refunded | ±0 |
| **Total** | | **+14.6c** |

So: the house has *not* consistently lost — but look at the distribution. Of the six markets
that actually traded, it **lost on three**, including the two thinnest (9.5c and 13.3c of
volume), and made almost all of its profit on the single market that had real two-way volume
(11 distinct traders, 61 trades, 158.6c of volume). That is exactly the shape the critique
predicts: subsidised liquidity is profitable when the market is genuinely contested and a slow
leak when it is not.

---

## 4. The single-side rule

**The rule.** On a binary market (two options — every "who wins" market), a player may hold an
open *directional* position on only one option at a time. Trying to buy the other side, or to
place a buy limit on it, returns an error and changes nothing.

**Why.** A pattern of holding both sides and then unwinding only one of them back into the pool
is a near risk-free hedge, and the cost of that hedge lands entirely on the liquidity
providers. We found 15+ historical cases before the rule existed. The rule is the dike.

**What it does *not* restrict:**

- **Selling is never blocked.** You can always exit.
- **Re-buying the same side** is always fine — average up or down freely.
- **Multi-option markets are exempt.** Spreading across 3+ outcomes is legitimate forecasting,
  not a hedge.
- **LP inventory doesn't count.** Shares you receive from providing liquidity are not a bet and
  can be sold at any time, and they never block you from betting the other side.

**On "isn't betting against your own team suspicious?"** This came up publicly during Round 2
and answered itself in the thread: the 10c win reward a tournament player gets for winning is
**larger than a typical bet's profit**, so a player buying the opposing side is *hedging their
own match outcome*, not throwing it. That is the same reason regulated sports markets worry
about players betting *on* themselves rather than against. We have not seen manipulation, and
the single-side rule already prevents the version of it that costs other people money.

---

## 5. Main-server markets (the public 24/7 server)

Anything on the public server is **player-created**. Nobody at BB opens markets for you; 18
different players have opened "who wins" markets so far. Four categories:

**Winner** — North vs South on the current game. One open at a time, server-wide. Resolves
automatically at game end.

**Duration** — how long the current game lasts, in five buckets (`<15m`, `15-30m`, `30-60m`,
`60-120m`, `120m+`). One open at a time. Resolves automatically at game end from the in-game
clock.

**Production** — "which side produces/loses more X". Pick from a fixed list of 37 items and
fluids, pick `produced` or `lost` (lost only works on placed entities), and pick the resolution
trigger: at game end, or at a specific game minute between 5 and 480. Resolves itself from live
game statistics. On a tie, or if the stats can't be read, the market is **cancelled and
everyone refunded** rather than resolved on a guess.

**Custom** — anything you can write a title for, 2 to 5 options, 1 minute to **4 weeks**
duration. These are the ones that need a human to settle them, which is why they are limited (2
open per creator) and why admins can resolve or cancel them. Typical use: "who will win next
weekend's tournament", "who will be the next captain".

Winner/duration/production markets carry a far-future nominal close time because their *real*
trigger is the game ending, not a clock. Custom markets lock automatically when their deadline
passes.

To trade: authenticate from in-game (the bot gives you a token that only you can see), then use
the website or the API. Every market page has a live price chart, an order book, a depth chart,
recent activity, and your position and P&L.

---

## 6. What is different in the 3v3 tournament

The BB Cup 3v3 tournament runs on a separate server with its own automated pipeline.
Betting works differently in five ways.

**1. Markets are house-created, one per scheduled match.** As soon as a fixture is scheduled
(and at least 15 minutes before kickoff), the tournament daemon opens a binary market titled
after the two teams and seeds it with **20c of house liquidity at 50/50**. You never have to
create it, and the odds are not pre-loaded with anyone's opinion.

**2. Betting stays open for the entire match.** This was a deliberate change on **13 July
2026**. Originally markets locked at kickoff. Now, while a match is `live`, the close time is
pushed forward on every reconciliation pass, so the odds actually move as the game develops.
This mirrors what the main-server winner markets already did, and it is what makes the live
match page interesting: the market price is plotted against the in-game production charts.

**3. Resolution is automatic from the match result.** When the result is detected, the market
resolves to the winning team. No human decides.

**4. Forfeits and cancelled fixtures are refunded, not settled.** If a match ends in a forfeit
or a bye — no game actually played — the market is **cancelled and every stake refunded**,
including collateral locked in resting limit orders. Nobody wins or loses coins on a match that
did not happen. (Rescheduled matches are followed automatically: the close time moves with the
fixture and a locked market re-opens.)

**5. Players are also paid 10c per win.** Every member of the winning roster who actually
played gets a flat 10c — 30c per match. This is separate from betting, and as noted in §4 it is
big enough to dominate typical bet sizes, which is why a player buying against their own team
reads as a hedge rather than as anything sinister.

**A real example — Round 2, Busbiters vs Chua** (the largest market to date):

- Opened 26 July 21:05 UTC with 20c of house liquidity at 50/50.
- **158.6c of volume**, 58 AMM buys and 3 sells from **11 distinct traders**. Three buy limit
  orders also rested on the book; none of them filled before the market closed.
- Prices genuinely oscillated: first trade moved it to 45.9/54.1 for Chua, it swung back over
  Busbiters through the following day, dipped to 50.8/49.2 in the final minutes, and closed at
  58.7/41.3.
- **1.59c burned** (exactly 1% of volume) and **22.07c** collected as LP fees.
- Busbiters won. Of the 132.69c of collateral in the market, winning shareholders were paid
  **97.02c** and the house LP received **35.67c** back on its 20c stake.

That is what the system looks like when it works: a contested market, real price discovery, and
liquidity that got paid for being there.

---

## 7. Known limitations, and the open question

### The lopsided-match problem — the critique is correct

The argument, restated fairly: on a very unbalanced match, the market opens at 50/50 with deep
house liquidity. The first informed bettor buys the favourite until fees make the next coin
not worth it. The price ends up at, say, 60/40. Now nobody has a reason to trade: backing the
favourite is no longer profitable after fees, and backing the underdog at 40% is absurd when
its real chance is 5%. The market freezes at wrong odds, the first bettor captured the
subsidy, and the house pays.

**We agree with the diagnosis.** It is a known failure mode of subsidised prediction markets:
the seed liquidity *is* a subsidy, and on a market with no genuine disagreement it goes to
whoever gets there first. Our own numbers above show it — the house lost on three of the six
3v3 markets that traded, the two thinnest among them, and made nearly all its money on the one
that was actually contested. And note from the worked example in §3 how the wedge appears: 2c
of buying moved the price 4 points but cost 0.606c per share
against a 0.541 mid — on a market where the "true" price is 0.95, the fee wedge bites long
before the odds get anywhere near correct.

Two honest caveats, not rebuttals:

- It is a **lopsided-match** problem specifically. On the close matches that make up most of the
  tournament, the current design works well — the Round 2 example above is exactly the
  behaviour we want, and it made the house money.
- The loss-compensation fee is not pure friction; it is what makes anyone willing to be an LP
  at all. Remove it and you need a different answer for who eats the loss.

### The proposal, and the trade-offs as we see them

The suggested fix is: **much thinner starting liquidity (≈10% of today), much more slippage,
no fees, and allow betting both sides.** The 10 first tokens on one side should be allowed to
slip the price to 90–95% immediately. Effects: backing the favourite becomes self-limiting,
backing the underdog becomes *rational* because you get odds that reflect reality, and the
house's subsidy shrinks to 1–2 coins per match.

We think this is a serious proposal. Here is where we think it needs work, stated as questions
rather than objections:

1. **Thin liquidity is also noisy and manipulable.** With a 2c pool (10% of today's seed), a
   fee-free 1c buy takes the price from 50% to about 69% — nearly 20 points on a single coin,
   and a 2c buy takes it to 80%. That makes the underdog attractive, yes — but it also means
   the displayed odds are mostly a record of who traded last, and the live match page's
   prediction line becomes close to meaningless. There is also a wash-trading concern: cheap
   price movement is cheap *false signal*, and prices are shown publicly in-game and on the
   live pages. Where is the line between "informative volatility" and "the graph is just noise"?
2. **Both sides needs a pool-protection answer.** We did not ban two-sided positions for fun —
   it was measured, with 15+ real cases. The proposal may well be immune (with no fees and a
   thin pool the economics of the hedge change completely, and with enough slippage the round
   trip is self-punishing). But that has to be *shown*, not assumed. This is the single point we
   will not drop without an analysis.
3. **Zero fees means zero burn.** This is the weakest of our objections and we'll say so: the
   trade burn has destroyed about 34c to date, against roughly 1,450c removed by the decay job,
   so it is not what holds the market cap down. What it *is* is the only sink that touches
   **active** players — everything else is paid by inactive holders. Removing it puts all of
   the anti-inflation work on people who stopped playing. Solvable, but it is a change to the
   coin economy, not just to the market.
4. **Who is the LP then?** With thin liquidity and no fees, "LP" stops being a role players
   would ever take voluntarily. That is arguably fine for house-funded tournament markets — but
   on the public server, *players* create and fund the markets. A design that only works when
   the house pays would effectively end player-created markets, which are the majority of all
   markets ever opened.
5. **Hybrid options exist.** Thin liquidity for tournament markets while keeping the current
   design for player-created ones; a starting price seeded from something other than 50/50 for
   obviously lopsided fixtures; a fee that decays as the market ages; or simply a much smaller
   house seed. These are cheaper experiments than a full redesign.

### Other things we already know are imperfect

- **LP withdrawals are frozen** until resolution. It is a blunt instrument that fixed a real
  abuse; a smarter time- or depth-weighted rule would be better.
- **The effective cost of a trade can exceed 1c per share** on a heavy favourite, which is
  always a losing purchase. There is a warning in the preview, but the engine does not yet
  refuse the trade outright.
- **Very low-volume markets are structurally unfair to whoever provided the liquidity.** Fees
  need two-way flow to work, and a market with three trades does not have it.
- **The 10c win reward and the betting market interact** in ways we did not design deliberately.
  It happens to make hedging benign, but it was not planned that way.

### One rule while the tournament is running

**No changes to the market mechanics mid-tournament.** The rules of a market do not change
between Round 3 and the final. Anything we adopt from this discussion ships after the
tournament ends.

---

## Proposals welcome

If you have read this far, you know more about BB Bets than almost anyone. The offer to write
up an alternative design is taken seriously — please do. Concretely, the most useful form would
be: proposed liquidity depth and slippage curve, what happens to fees and to the burn, what
protects the pool if two-sided positions come back, and what it does to player-created markets
on the public server.

Numbers welcome. We have the full trade history of every market ever opened and are happy to
backtest a proposal against it.
