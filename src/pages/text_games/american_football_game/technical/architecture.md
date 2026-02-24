# Architecture Overview

This document outlines the high-level architecture of the football simulation game, derived from the defined data models. The design is modular, data-centric, and built to support emergent complexity through interconnected systems.

## Core Design Principles

1.  **World as a Sandbox**: The `world` table is the root container for every simulation instance. This design ensures that each game save is a completely isolated and self-contained universe.

2.  **Composition Over Inheritance**: Character and entity design avoids rigid class hierarchies. A base `individual` table holds universal traits (name, personality), while specialized roles (`player`, coach, etc.) are defined in separate tables that link back via a one-to-one relationship. This provides maximum flexibility for creating diverse character types.

3.  **Generic Relationship Engine**: A single, polymorphic `relationship` table is used to model the connections between any two entities. By storing the type and ID of each entity, it can manage everything from individual friendships and rivalries to a team's rivalry with another team, creating a rich web of interactions.

4.  **Cascading Modifier System**: The game world's characteristics are determined by a hierarchical system of modifiers. Values at a lower geographic level (e.g., a `City`) are calculated by combining its own modifiers with the effective values of its parent (`State`, `Country`). This applies to economics (`growth_rate`, `wealth_modifier`), culture (`football_interest_modifier`), and more, creating nuanced regional differences.

## System Architecture Breakdown

### Geography & Economy
The world is defined by a `World -> Country -> State -> City` hierarchy. The `state` level is optional to accommodate different national structures. This hierarchy is the foundation for the **Cascading Modifier System**, which dynamically calculates local attributes for:
- **Economy**: `global_growth_rate`, `wealth_level`, `cost_of_living_modifier`.
- **Culture**: `football_interest_modifier`.
- **Environment**: `avg_temperature`.

This model ensures that a city's economic and cultural landscape is a logical result of its state, country, and global context.

### Leagues & Competitions
The `league` model is designed for high configurability:
- **Structure**: Leagues can be customized with optional `conferences` and `divisions`.
- **Scope**: A league can operate within a single country or span multiple nations via the `league_country` link table.
- **Rules Engine**: A flexible `league_membership_rule` table allows for defining specific requirements for teams to join (e.g., financial stability, geographic location).
- **Competition**: Standardized `playoff_format` records allow for different kinds of postseason structures.

### Teams & Organizations
The `team` is the central operational entity for the user.
- **Identity**: Defined by branding (name, colors, logo) and its home `city_id`, which anchors it geographically and ties it into the cascading modifier system.
- **Affiliation**: A team can exist independently or belong to one or more leagues via the `team_league` table, including assignments to specific divisions/conferences.
- **Personnel**: A team's roster will be composed of `player` entities. Leadership roles (owner, coach) link to the `staff` table, which in turn connects to an `individual` record, allowing access to their specific attributes and personality traits.

### People & Roles
This system is built on the principle of **Composition Over Inheritance**.
- **Individual**: The foundational model for every person. It stores universal data: identity (`first_name`, `last_name`), origin (`home_city_id`), and a suite of personality ratings (`desire_to_win`, `greed`, `loyalty`) that will drive the AI for decision-making and narrative events.
- **Player**: A specialized role linked to an `individual`. This table stores all data relevant to on-field performance: physical and mental athletic ratings, status, and links to their career statistics via the `player_stats_history` table.

---

## Simulation Engine Architecture

This section outlines the conceptual model for simulating a single play. The design is a discrete, tick-based system where player actions are resolved in small time increments.

### 1. The Field Model

A virtual representation of the football field is required. It will be a 2D coordinate grid.
- **Dimensions**: 120 yards long (including two 10-yard endzones) by 53 1/3 yards wide.
- **Coordinates**: A coordinate system (e.g., `x, y`) will track the precise location of every player and the ball. The `x` axis represents the length of the field (yard lines), and the `y` axis represents the width (from sideline to sideline).
- **Game State Context**: The field model also includes critical game state information, such as the line of scrimmage, the first down marker, and hash mark location of the ball.

### 2. Single Play Simulation Lifecycle

Simulating one offensive play can be broken down into three phases: Setup, Execution Loop, and Resolution.

#### Phase I: Pre-Play Setup

Before the simulation loop begins, the initial state is established.
1.  **Define Initial Conditions**: The current down, distance, and ball position (yard line) are loaded from the game state.
2.  **Player Placement**: The 22 players on the field are placed at specific starting `(x, y)` coordinates based on the offensive and defensive play formations that were selected.
3.  **Assign Roles**: Each player is given an initial assignment based on the play call. For example:
    -   **Offense**: A QB has an assignment to "pass to WR1", a lineman to "block DE", a running back to "run to the 3-hole".
    -   **Defense**: A cornerback has an assignment to "cover WR1", a linebacker to "blitz the A-gap".

#### Phase II: The Execution Loop (Tick-Based Simulation)

The play unfolds as a series of "ticks," which are small, discrete units of time (e.g., 0.1 seconds). The loop continues until a play-ending condition is met. In each tick, every player on the field is processed:

1.  **Perception & Intention**: Each player AI assesses its situation.
    -   *Where am I? Where is my assignment? Where is the ball?*
    -   Based on its assignment and the state of the field, the player forms an immediate intention (e.g., "move towards the ball carrier," "continue my route," "engage the blocker in front of me").

2.  **Action Selection & Resolution**: The player's intention is translated into an action.
    -   **Movement**: A target `(x, y)` coordinate is calculated. The player moves towards it, with the distance covered in that tick determined by attributes like `speed` and `explosiveness`.
    -   **Interaction**: If a player's position causes them to interact with another player (e.g., a blocker and a defender), an interaction check is triggered. The outcome is calculated by comparing relevant player ratings (e.g., `blocking_power` vs. `shed_blocks`; `breaking_tackles` vs. `tackling`). The result could be a successful block, a shed block, a broken tackle, etc.

3.  **State Update**: The engine updates the state of the world based on all resolved actions. The ball's position is updated if it has moved, and all players have new coordinates.

4.  **Check for End Condition**: The engine checks if the play is over. Terminating conditions include:
    -   Ball carrier is tackled (positional check and tackle status).
    -   Ball carrier goes out of bounds (positional check).
    -   A touchdown is scored.
    -   An incomplete pass occurs.

If a condition is met, the loop terminates. Otherwise, the next tick begins.

#### Phase III: Post-Play Resolution

1.  **Determine Final Result**: The engine calculates the final outcome (e.g., 5-yard gain).
2.  **Update Game State**: The down, distance, and ball position are updated for the next play.
3.  **Record Statistics**: Relevant stats (e.g., passing yards, tackles) are recorded for the players involved, which will be aggregated into the `player_game_stats` table at the conclusion of the game.

---

## Entity Generation Architecture

To fulfill the roadmap goal of entity generation, the architecture uses a configurable, data-driven system. This avoids hard-coding generation logic and allows for dynamic evolution of the game world. The system is composed of three key data models:

1.  **Archetypes**: These are templates that define a "type" of entity. For a player, archetypes could include "Pocket Passer QB," "Power Running Back," or "Ball-Hawking Safety." Each archetype is linked to a set of baseline attributes (e.g., a Pocket Passer has high `throwing_accuracy` but low `speed`).

2.  **Archetype Attributes**: This model stores the specific baseline integer values for every attribute of an archetype. The simulation engine reads these values and applies a randomization factor to create variation among newly generated individuals of the same archetype.

3.  **Generation Configuration**: This model acts as the control layer. It dictates which archetypes are available to be generated within a specific context and how common they are. The context can be defined by:
    -   **Scope**: Configurations can be applied globally (`world`), to a specific `league`, or to a `country`, allowing for regional differences in generated talent.
    -   **Time**: Each configuration has a `start_year` and optional `end_year`, enabling the types of players generated to evolve as the game progresses through different eras.
    -   **Frequency**: A `spawn_weight` attribute determines the relative probability of an archetype appearing, making it possible to have rare or common types of players.

In practice, the system would use a `generation_config` to determine *that* it should generate a 'Pocket Passer QB' for a specific league, then use the `archetype` and `archetype_attribute` tables to find the baseline stats (e.g., `throwing_accuracy` = 85), and finally apply randomization to create a unique player.
