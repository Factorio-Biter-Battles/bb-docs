---
title: BB Captains — the captains lobby and server
slug: bb-captains
section: captain
order: 15
summary: Captains games on demand — sign up in game or on the website, volunteer as captain, get called in game, pick and play on the BB Captains server.
updated: 2026-09-21
icon: "🧢"
---

BB Captains brings the classic **captains game** (two captains pick their teams, then play) to a
lobby that works like the [BB League lobby](../league/how-it-works.md), on its own dedicated
server. The weekly organised captain games are described in
[Captain games](captain-games.md).

## The lobby

You can sign up in game or on the website. Both show the same queue, live.

**In game:** the **BB CAPTAINS (N)** button, next to the BB League button on the main and League
servers, opens the **BB CAPTAINS LOBBY** panel (N is the number of players waiting). It shows:

- how many players are waiting and how many volunteered as captain, and whether a game is ready
  or what is still missing (for example "need 2 captain volunteers");
- **I play from N players**, with `-` / `+`: the smallest game you're happy to play (2, 4, 6, 8,
  10, 12, 14 or 16 players);
- **I want to be captain**: click it to volunteer;
- **Sign up / update** and **Leave**;
- the **Join BB Captains server** button;
- how many players are waiting in the BB League lobby, and the list of players signed up.

An in-game sign-up lasts 3 hours. Clicking **Sign up / update** again renews it and keeps your
place in the queue.

**On the website:** the **BB Captains** page shows the live counts and has the same form (**I
play from N players**, **I want to be captain**, and how long you're available). You need to be
logged in (type `askbot auth` in a Free BB server's chat, then paste the token on the site). The
home page also has a BB Captains card: players in the lobby, server status and the last games.

## When a game starts

- A game is called when enough players are waiting whose minimum allows that game size, **and
  at least two of them volunteered as captain**. The game is the biggest size that works, always
  **2 teams**.
- The two captains are the volunteers among the drawn players who **signed up first**.
- Without two captain volunteers there is no game, and the lobby says why.
- Only one captains game runs at a time.

## The call

- There is no Discord DM: the call is **in game**. If you're drawn and on the main or League
  server, the BB Captains panel opens by itself with "Your BB Captains game is called: join the
  BB Captains server now".
- Click **Join BB Captains server**: your game switches you to the Captains server. There is no
  game password.
- The game begins when both captains and all the drawn players are connected. If someone is
  missing, the call is repeated after 5 minutes, and again after 10; if players are still
  missing after about 15 minutes, the game is cancelled.

## Picking and the game

- The server starts a fresh map, and a bot referee runs the usual captains event: a 2-minute
  captain countdown, then the captains pick their teams, then the preparation and the game,
  **North vs South**.
- If there are fewer than two captains when the countdown ends, the game is cancelled and the
  map is reset.
- Anyone else who connects is a spectator.
- Every game gets its own page on the website (**BB Captains game #…**), with both captains, and
  a list of all captains games. Captains games are not counted in the main server's statistics
  and are not League-rated.

## Players who don't come: the 15-minute pause

If a called game is cancelled **before picking starts** (not enough captains, or players didn't
join in time):

- players who **were** on the Captains server keep their sign-up and their place in the queue;
- drawn players who **were not** there keep their sign-up but **can't be drawn for 15
  minutes**. The panel shows "paused N min (absent)" next to their name, and a paused volunteer
  doesn't count as a captain. Signing up again doesn't end the pause. If your sign-up runs out
  during the pause, it is withdrawn.

## Good to know

- You can be in the League lobby and the Captains lobby at the same time, but you are never
  drawn by both at once. A League draw withdraws your Captains sign-up. A captains call holds
  your League sign-up, which comes back if the call is cancelled before picking and is
  withdrawn once picking starts.
- Players in a League match about to start are never drawn.
- Captains games are **two captains, two teams**. Captains games with 3 or 4 teams will come
  later.
