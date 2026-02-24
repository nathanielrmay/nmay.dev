# Feature Catalog

This document uses a [MoSCoW matrix](https://en.wikipedia.org/wiki/MoSCoW_method) to guide development priorities and describes key game systems in more detail.

## MoSCoW Prioritization

This section uses the MoSCoW method (Must-have, Should-have, Could-have, Won't-have) to prioritize features for the Minimum Viable Product (MVP) and beyond.

### Must Have (for MVP)

These features are non-negotiable for the first playable version. The game is not functional without them.

- **Core Simulation Engine**: Ability to simulate a single play, a drive, and a full game, producing text-based logs and box scores.
- **Data-Driven Characters**: `Individual` and `Player` models with attributes that directly influence simulation outcomes.
- **Simplified League Structure**: A single, functional league with a generated schedule and a championship game.
- **Entity Generation**: Logic to create new players and teams to populate the world, driven by `Archetype` and `Generation_Config` models.
- **Simplified Playbook System**: Pre-set plays are used by AI coaches; no user-facing play editor.
- **Basic Developer Interface**: A functional, likely command-line, interface to run simulations and browse NPCs.

### Should Have (Post-MVP Must-Haves)

These features are essential for the complete game vision but will be prioritized for development immediately following the MVP.

- **Advanced Playbook System**: A user-facing tool to create and edit detailed plays and formations.
- **Detailed Financial Model**: Team budgets, player salaries, revenue from tickets and marketing, and operational expenses.
- **World & Economy Foundation**: A functioning `World` model with a cascading economy (`Country`, `State`, `City`) to provide context.
- **Basic Financial Reporting**: Generation of simple team financial reports.
- **Player Progression & Aging**: A system for player attributes to develop and decline over their careers.
- **Core Off-Season Logic**: Implementation of key off-season phases like player retirement and new player drafts.
- **LLM-Powered Narrative Engine**: Use of an LLM to generate rich narrative content, including game summaries, player news (awards, injuries), dynamic rivalries, media interactions, and RPG-centric events.
- **Character Life-Sim**: A system for managing the playable character's personal life, including relationships, health, and stress, with cascading effects on job performance and a direct impact on the character's **Personal Legacy Score**.
- **Generational Play**: Ability to continue the game as the playable character's offspring after death or retirement, a core component of building a multi-generational **Legacy Score**.
- **Graphical User Interface (GUI)**: A full graphical interface for team and league management.
- **Asynchronous Multiplayer**: A "league hosting" service where multiple users manage teams in the same game world.
- **Advanced Scouting System**: Detailed scouting reports, combine data, and discovery of hidden player potential.
- **In-depth Staff Roles**: Hiring and managing of coaches, coordinators, and scouts who have their own attributes.

### Could Have (Desirable Extras)

Desirable features that would improve the game but are not a priority for the initial full release.

- **Historical Modes**: Ability to start simulations in past eras with historical data.

### Won't Have (For Now)

Features explicitly excluded to prevent scope creep and maintain focus.

- **Real-time 3D Play Visualization**: Gameplay will be presented through text and statistics, not a 3D engine like Madden.
- **Synchronous Multiplayer**: Multiplayer will not be real-time, head-to-head gameplay.
- **Official Licensing**: The game will use fictional players, teams, and leagues.

## Feature Descriptions

This section provides more detailed outlines for major features and game systems.

### NPC & Character Roles

The world will be populated by a variety of Non-Player Characters (NPCs), each with their own attributes, personalities, and roles. All NPCs are built on the base `Individual` model.

#### Team Personnel

- **Players**: The athletes who play in the games. They have detailed physical, mental, and skill-based attributes.
- **Head Coach**: Manages the coaching staff and has ultimate authority on game-day strategy. This is a **primary** role for the playable character.
- **Offensive/Defensive/Special Teams Coordinators**: Specialized coaches responsible for their respective phases of the game. Their schemes and play-calling tendencies influence team performance.
- **General Manager**: Responsible for player contracts, trades, and drafting. This is a **primary** role for the playable character.
- **Team Owner**: The ultimate authority for the team. Sets budgets, long-term goals, and can hire/fire the GM. This is a **primary** role for the playable character.
- **Commissioner/Observer**: While not a direct management role, the player can choose to act as an uninvolved observer, watching the simulation unfold from a league-wide perspective without being tied to a specific team.
- **Scouts**: Evaluate talent from other leagues and in the upcoming draft class.
- **Medical Staff**: Manage player injuries and rehabilitation.

#### External World NPCs

- **Agents**: Represent players during contract negotiations.
- **Media Personnel**: Journalists and reporters who write stories, conduct interviews, and influence public perception.
- **Family Members**: Spouses, children, and other relatives who are part of the player character's life-sim narrative.
