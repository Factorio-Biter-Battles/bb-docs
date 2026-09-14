---
title: Loans — borrow from another player, repay with a fee
slug: bets-loans
section: bets
order: 40
summary: Peer-to-peer coin loans — the flow, the 10% / 20% fee, the red flag, and how garnishment works on default.
updated: 2026-09-14
icon: "🤝"
---

Any player with a coin account can lend to any other player. Loans are **peer-to-peer**: the
system only handles escrow, bookkeeping, and — if you don't repay on time — automatic
garnishment of your future win / MVP bonuses.

## Flow

1. **Borrower requests**: `askbot borrow <lender> <amount> <days>`. A pending loan is
   created. Nothing moves yet.
2. **Lender confirms**: `askbot loan confirm <id>`. The principal is debited from the lender
   and credited to the borrower atomically. The due date is set to *now + days*.
3. **Borrower repays on time**: `askbot loan repay` before the due date. Borrower pays back
   **principal × 1.10** (10% fee → lender's interest). Loan closes.
4. **Or: due date passes**. The cron tries to auto-debit the borrower's balance at the 10%
   rate. If the balance covers it, the loan closes cleanly. Otherwise:
5. **Default**. Target repayment is bumped to **principal × 1.20** (20% fee), the borrower is
   **red-flagged**, and garnishment begins.

## Red flag — what it blocks

While you have an unpaid defaulted loan:

- **Blocked:** market buys (`askbot buy`, web buy), adding liquidity (`askbot lp add`).
- **Still allowed:** selling shares, removing liquidity — so you can unwind existing
  positions to repay manually.
- **Coin tips** (`askbot coins tip`) are still allowed; use carefully, they don't auto-clear
  the debt.

## Garnishment

Every time you receive a **win bonus** (normal game or captain game) or an **MVP bonus**
while red-flagged, the coins are redirected straight to your lender until your target (120%
of the principal) is reached. Any surplus on the credit that would overshoot the debt stays
with you — the garnishment stops the moment the loan is fully repaid and the red flag clears
automatically.

Every garnishment shows up in your transaction history as a `loan_garnish` debit paired with
the original bonus credit, so your PnL trail stays auditable.

## Rules & limits

- **One open loan per borrower** at a time (pending, active, or defaulted counts). A lender
  can have any number of outgoing loans.
- **Minimum principal:** 1 coin. **Duration:** 1 to 30 days.
- **Pending loans expire** after 3 days if the lender hasn't confirmed (status →
  `expired`).
- **No self-lending**, and the lender must have enough balance at confirm time.
- **No collateral** on your existing positions — they're not seized on default. Only future
  win/MVP bonuses are.
- **No interest during the loan window** — only the 10% / 20% flat fee at closure.

## Examples

```text
askbot borrow alice 50 7          # request 50c from alice, 7-day loan
askbot loan confirm 42             # alice approves loan #42
askbot loan repay 42               # bob repays early → 55c paid, 5c profit for alice

# or if bob doesn't repay:
# day 7 → auto-debit attempt fails → status='defaulted', red flag ON
# each subsequent win/mvp credit to bob auto-garnishes to alice
# until alice has received 60c total (50 + 20% fee), flag clears
```
