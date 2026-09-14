---
title: Builds & production — burner city → power → smelting → oil → lasers
slug: gameplay-builds
section: gameplay
order: 40
summary: Production guidance from burner city through power, smelting, mixed ore, oil, nuclear and lasers.
updated: 2026-09-14
icon: "🏭"
legacy_anchors: [wiki-builds]
sources: [https://freebb.miraheze.org/wiki/Main_Page]
---

## Burner city (the start)

Each player starts with 10 burner miners. Most teams expand to cover at least one iron,
copper and coal patch. Burner miners are **more cost-effective than electric** in the early
game (1 coal of input pays itself back). Don't remove burners while there's still iron in
main — counts as soft-griefing. Many players prefer left-to-right placement (mouse motion +
burner coal hunger).

## Power

- Power is the easiest place to contribute as a new player — the BP is simple and brownouts
  hurt everything.
- Factorio 2.0: **1 water pump** is enough for more boilers than you'll bother to wire.
  Burner inserters are safest in case of brownout.
- **1 full yellow belt of coal supports ~33 boilers at full power.** Solid fuel = 12 MJ vs
  coal's 4 MJ → 1 belt of solid fuel can feed ~100 boilers (sushi-pipe / direct insertion).
- Buffer some coal so players can grab it for grenades / capsules / topping up boilers.

## Smelting

- Two common designs: 12-long (one yellow belt out at steel furnaces) and 24-long (main
  standard, two belts in / two belts out at steel).
- Pre-belt-saturating: 30 miners per yellow belt without mining-prod, 28 with prod-1, 25
  with prod-2. *Don't research mining prod 3* — too expensive.
- Compress unfull belts *before* the smelter (real compressors, not balancers — balancers
  just spread the unfull-ness).
- Steel smelters need 2 lanes of input; place buffer chests on the output, steel is always
  scarce.

## Mixed ore

Patches typically run a 6:5:2:2 iron:copper:stone:coal ratio (not guaranteed). Pros: all
ores in one place, cheap basic intermediates. Cons: unbalanced consumption clogs the system.
Strategies:

- **Pre-smelt sort** — splitters sort the mixed belt into pure-ore lanes. Clogs if you don't
  actually use all 4. Box overflow.
- **Post-smelt sort** — full mixed belt past furnaces, inserters grab whatever they can;
  sort plates+bricks afterwards. Filter coal before or after.
- **Post-smelt sushi** — assemblers next to a sushi belt grab whatever they need. Box
  overflow at the end.
- Mix with pure ore when you need; drain excess copper into LDS / mass green chips, excess
  stone into walls / landfill, excess coal into buffered power.

## Oil & refineries

- 10 refineries = 2000% crude → start there; expand modularly with bots.
- Stage 1 = basic oil. Stage 2 = pre-build the advanced layout while researching. Stage 3 =
  swap to advanced oil (more petrol + heavy + light).
- Refinery → cracking ratios: **12-10-3** (12 refineries / 10 light-cracking / 3
  heavy-cracking) or **3-3-1**. For 100% petrol throughput at 4000% crude: 20-17-5 (20 ref /
  22 chem total).
- Place tanks at the end of every output pipe to buffer.
- **Circuit-controlled cracking** — pump + tank + circuit condition stops light-oil cracking
  automatically when light demand spikes (solid/rocket fuel). Mandatory if you use solid
  fuel for power.
- Pipeline limit: all connected pipes in a 320×320 box; need a pump to split networks beyond
  that.
- Common oil mistakes: no motors, no power poles, not enough steel for pumpjacks,
  disconnected pipes, missing one power pole on a long line.

## Nuclear (late-game only)

- Dedicated nuke outpost: reactor running in **~90 min from scratch**; cold-start of the
  U-235 rolls takes 12–25 min.
- 20 miners + 6 centrifuges (or 4.1 with speed-1 modules) continuously fuel 6.7 reactors.
  Petrol/acid use is negligible — **set up local acid production**, don't depend on someone
  else's pipes.
- 2×2 reactor = 480 MW (8 coal plants). 3×2 = 800 MW. Single reactor alone is weaker than 1
  coal plant.
- 2×2 build cost: ~37k iron + 18k copper + 2600 RC + same amount of concrete (4 asm2 = 14
  min). Plan for it.
- Multiple smaller (2×2 / 2×3) reactors > one big — biter side-attack risk.
- Heat pipes have a throughput limit; don't space reactor + steam exchanger too far apart.

## Lasers

- Tileable / modular designs flip the late game.
- From a standard 2-lane steel smelter: 1 lane iron→steel supports ~4 asm2 lasers + 8
  battery chemplants (no prod). 8 asm2 of lasers needs 5 refineries — main's refining will
  deplete fast if you don't expand it.
- From mixed ore: 2 lanes mixed → at most 2–3 asm2 of lasers; iron ratio is too low.

## Attack / rush builds

- Any single module from a main base can be turned into a science-spam outpost: green / mil
  sci on higher difficulties, purple / yellow on lower.
- "Green Dragons" = chest-fed green sci, ~50 SPM per module.
- Hand-fed mil sci is a strong rush on high difficulties.
- Rocket outposts (LDS / BC mixers) are the late-game version.
- Rushes benefit from **targeted research** + corner-cutting (basic oil instead of advanced,
  hand-fed steel outposts, etc).
