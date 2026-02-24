# Glossary of Terms

This document defines key terms and concepts used throughout the project's planning and design documents. It is intended to provide clarity on game-specific mechanics, technical jargon, and potentially ambiguous terms.

---

### A

**Archetype**
: A template or blueprint used for generating new game entities, primarily players. An archetype, such as "Pocket Passer QB" or "Coverage Safety," defines a set of base attributes and skills that the generation system uses to create new, varied individuals.

---

### B

**Box Score**
: A standardized statistical summary of a single game. It includes individual player statistics (passing, rushing, receiving, etc.) and team-level totals. The `player_game_stats` table is the source for this data.

---

### C

**Cascading Modifier System**
: A core architectural principle where attributes for a geographic area are calculated by inheriting and modifying values from parent regions. For example, a city's economic growth is determined by applying its local modifier to its state's effective rate, which in turn is derived from the country's rate and the world's global rate. This creates nuanced regional differences.

**CRUD**
: An acronym for **C**reate, **R**ead, **U**pdate, **D**elete. These are the four basic functions of persistent storage and are used to describe the fundamental operations for managing data in a database.

---

### D

**Depth Chart**
: A strategic document that arranges players from the team roster into specific positions and ranks (e.g., starter, backup) for a given situation or formation (like 'Shotgun Offense' or 'Nickel Defense'). A team can have multiple depth charts. Managed via the `depth_chart` and `depth_chart_position` tables.

**Down**
: A single attempt (a play) by the offense to advance the football. The offense gets a series of four downs to gain at least ten yards. If successful, they earn a "first down" and a new set of four downs.

**Drive**
: A continuous series of offensive plays by one team, starting from when they gain possession of the ball until they score, lose possession via a turnover, or punt the ball to the other team.

---

### E

**Entity**
: A generic term for any distinct object or record within the game's simulation world. Examples include a `player`, `team`, `league`, `individual`, or `city`.

**Entity Generation**
: The process of programmatically creating new entities to populate the game world. This is primarily used for generating new players each off-season (the "draft class" or "youth intake") to ensure the game world continues indefinitely. This system is driven by `Archetype` and `Generation Configuration` models.

---

### I

**Individual**
: The base data model for every person in the game world. It stores universal data like name, birth date, and personality traits. Other models like `Player` or `Staff` link to an `Individual` record to add role-specific attributes.

---

### L

**Legacy Score**
: The primary measurement of success in the game. It is a comprehensive score that combines a player's **Professional Legacy** (wins, championships, finances) with their **Personal Legacy** (relationships, family, health) to provide a holistic evaluation of their career and life.

**Line of Scrimmage**
: An imaginary line across the width of the field where the football is placed at the beginning of a play. Neither the offense nor the defense can cross this line until the play begins.

---

### M

**MVP (Minimum Viable Product)**
: The earliest version of the game that includes just enough features to be usable and functional. The goal is to create a core, playable experience upon which further features can be built. See `../project/mvp.md` for specifics.

---

### O

**Off-Season**
: The period in the game's calendar between the final championship game of one season and the start of the next. It is comprised of several distinct phases, including player retirement, awards, new player drafts, and free agency.

---

### P

**Play Design**
: The definition of a single, specific football play concept, such as a "HB Dive" or "Slant Pass." It is a component of a `Playbook`.

**Playbook**
: A collection of `Play Designs` that a team's AI coach can choose from during a game.

**Polymorphic Relationship**
: A database design where a single table can link to records from multiple other tables. In this project, the `relationship` table is polymorphic because it can connect an `individual` to another `individual`, a `team` to another `team`, or an `individual` to a `team`, all within the same structure.

**Prestige**
: A numerical rating (1-100) assigned to entities like leagues and teams to represent their reputation, historical success, and desirability. High prestige attracts better players and more fan interest.

---

### R

**Relationship Engine**
: The system, centered on the `relationship` table, that models the connections between any two entities in the game. It tracks things like friendships, rivalries, family ties, and employment, each with a strength score that can influence entity behavior and narrative events.

---

### S

**Schema**
: The blueprint or structure of the database. It defines all the tables, the columns within those tables, their data types, and the relationships (foreign keys, constraints) between them. The project's schema is defined in `../technical/data_models.md`.

**Special Teams**
: A term for the group of players who participate in kicking plays (punts, field goals, kickoffs, and extra points).

---

### T

**Test Harness**
: A script or collection of software tools used by developers to test a component of the system. For this project, it refers to a simple script used to verify database connectivity and the correctness of CRUD operations.

**Tick-Based Simulation**
: The architecture for simulating a single football play. The play unfolds in discrete time intervals called "ticks" (e.g., 0.1 seconds). In each tick, every player on the field perceives the environment, decides on an action, and the engine resolves all actions simultaneously. The simulation continues, tick by tick, until a play-ending condition is met.

---

### W

**World**
: The top-level container for a single, self-contained game save. Each `world` record in the database represents a unique and isolated simulation universe with its own timeline, teams, players, and history.
