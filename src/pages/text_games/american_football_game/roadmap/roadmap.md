# Development Roadmap

This document outlines the major milestones for building the football simulation game. Each milestone builds upon the previous one, progressively adding layers of functionality to create a complete simulation experience.

The first six milestones collectively represent the work required to deliver the core features outlined in the **[Minimum Viable Product (MVP)](mvp.md)**. Milestone 7, the Off-Season Simulation, will be the first major feature implemented after the MVP is complete, paving the way for long-term replayability and the future introduction of role-playing elements.

## Table of Contents
- [Milestone 1: Foundational Scaffolding](#milestone-1-foundational-scaffolding)
- [Milestone 2: Entity Generation](#milestone-2-entity-generation)
- [Milestone 3: Single Play Simulation](#milestone-3-single-play-simulation)
- [Milestone 4: Drive (Set of Downs) Simulation](#milestone-4-drive-set-of-downs-simulation)
- [Milestone 5: Full Game Simulation](#milestone-5-full-game-simulation)
- [Milestone 6: Season Simulation](#milestone-6-season-simulation)
- [Milestone 7: Off-Season Simulation](#milestone-7-off-season-simulation)

---

## Milestone 1: Foundational Scaffolding

### Explanation
This initial phase involves setting up the core project structure, database, and a basic interface for developer interaction. It's about building the foundation upon which all other features will be built.

### Discussion
The primary goal is to translate the schemas in `technical/data_models.md` into a functioning database. A basic interface (likely a command-line tool or simple test harness) will be needed to perform CRUD (Create, Read, Update, Delete) operations on core tables like `world`, `country`, and `league`. This allows for initial data setup and testing.

### Observations
- The interface at this stage is purely for development and testing, not for end-users.
- Focus on data integrity, foreign key relationships, and ensuring the database schema is robust.

## Milestone 2: Entity Generation

### Explanation
Implement the logic to dynamically create game entities, specifically `Team` and `Player` records. This milestone brings the `Entity Generation Models` (`Archetype`, `Generation Configuration`) to life.

### Discussion
A generation module will be built to:
1.  Create `Team` records and populate their basic information.
2.  For each team, generate a roster of `Player` entities. This involves creating an `individual` record and a corresponding `player` record.
3.  The generation logic will be driven by `archetype_attribute` data, using the `base_value` plus randomization to create varied players.

### Observations
- As noted in the original plan, the initial generation does not need to be perfectly balanced. The focus is on creating the scaffolding to populate the database with test data, which can be manually triggered.

## Milestone 3: Single Play Simulation

### Explanation
Develop the core simulation engine capable of executing a single offensive play, from snap to whistle.

### Discussion
This is the implementation of the `Simulation Engine Architecture`. It involves a tick-based loop where each of the 22 players on the field is processed. In each tick, players perceive their environment, decide on an action (move, interact), and their actions are resolved. The loop terminates when a play-ending condition (tackle, touchdown, out-of-bounds) is met.

### Observations
- **What happens between plays?** This is a critical transition. After the simulation loop ends, the results must be persisted. The `game` state (down, distance, ball position) is updated, and a new record is created in the `play` table to log what happened.

## Milestone 4: Drive (Set of Downs) Simulation

### Explanation
Chain together multiple "Single Play Simulations" to create a continuous offensive drive. This includes handling first downs and changes of possession.

### Discussion
A new, higher-level loop will manage a series of plays. After each play, this "drive manager" will check the game state to determine the result:
- Was a first down achieved?
- Was it a turnover?
- Was it a scoring play?
- Is it now 4th down?

### Observations
- This is where 4th down decision-making AI (punt, field goal, go for it) becomes necessary.
- Simulating special teams plays (punts, field goals, kickoffs) is a prerequisite for a complete drive simulation, especially for handling scoring and changes of possession.

## Milestone 5: Full Game Simulation

### Explanation
Expand the simulation to cover a full game, including clock management, quarters, halftime, and overtime.

### Discussion
A "game loop" will wrap the "drive simulation" logic. This top-level loop is responsible for managing the game clock, advancing it based on the outcome and duration of each play. It will handle events like the opening kickoff, halftime, quarter breaks, and the final whistle.

### Observations
- **What happens between games?** After a game concludes, a post-game process must run. This involves finalizing statistics, updating team win/loss records, and laying the groundwork for the narrative engine by triggering basic player morale changes.

## Milestone 6: Season Simulation

### Explanation
Simulate an entire league season from week 1 through the playoffs and championship game.

### Discussion
This involves creating a season scheduler that generates a full schedule of games based on the teams in a `League`. A "season loop" will iterate week-by-week (`world.current_week`), triggering the "Full Game Simulation" for all games in that week. It must also track league standings.

### Observations
- After the regular season is complete, the logic must identify qualifying teams and initiate the playoffs according to the `playoff_format` defined for the league.

## Milestone 7: Off-Season Simulation

### Explanation
Implement the series of events and phases that occur between the end of one season and the beginning of the next.

### Discussion
The off-season is the core of long-term franchise management and world evolution. It is not a single process but a sequence of distinct phases that must be executed in order:
1.  Player Retirements & Staff Changes
2.  End-of-Season Awards
3.  New Player Generation (Draft / Youth Intake)
4.  Free Agency & Contract Negotiations
5.  Pre-season / Training Camp

### Observations
- **What happens between seasons?** This is where the game world truly evolves. The `generation_config` models are critical for creating new talent pools. Player aging and attribute progression/regression systems must be processed. This phase is essential for long-term replayability.
