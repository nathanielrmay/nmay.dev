# Football Simulation Game - Data Models



## Table of Contents

- [Core World & System Models](#core-world--system-models)
  - [World Model](#world-model)
  - [Relationship Model](#relationship-model)
- [User & Access Control Models](#user--access-control-models)
  - [User Model](#user-model)
  - [Role Model](#role-model)
  - [User World Role Model](#user-world-role-model)
- [Geographic Models](#geographic-models)
  - [Country Model](#country-model)
  - [State Model](#state-model)
  - [City Model](#city-model)
- [League & Competition Models](#league--competition-models)
  - [League Model](#league-model)
  - [League Standings Model](#league-standings-model)
- [Team & Organization Models](#team--organization-models)
  - [Team Model](#team-model)
  - [Playbook Model](#playbook-model)
  - [Play Design Model](#play-design-model)
  - [Stadium Model](#stadium-model)
- [Entity Generation Models](#entity-generation-models)
  - [Archetype Model](#archetype-model)
  - [Generation Configuration Model](#generation-configuration-model)
- [People & Character Models](#people--character-models)
  - [Individual Model](#individual-model)
  - [Player Position Model](#player-position-model)
  - [Player Model](#player-model)
- [Personnel, Contracts & Staff Models](#personnel-contracts--staff-models)
  - [Player Contract Model](#player-contract-model)
  - [Player Contract Year Model](#player-contract-year-model)
  - [Staff Role Type Model](#staff-role-type-model)
  - [Staff Model](#staff-model)
  - [Financial Advisor Attributes Model](#financial-advisor-attributes-model)
  - [Legal Advisor Attributes Model](#legal-advisor-attributes-model)
  - [Personal Wellness Attributes Model](#personal-wellness-attributes-model)
- [Game & Simulation Models](#game--simulation-models)
  - [Game Model](#game-model)
  - [Game State Model](#game-state-model)
  - [Game Plan Model](#game-plan-model)
  - [Play Model](#play-model)
  - [Player Game Stats Model](#player-game-stats-model)

---

## Core World & System Models

These models define the foundational elements of the simulation universe. All data resides in a single Postgres database.

### World Model

The World table represents a single game universe/save file. There is only one world record per game instance, serving as the root container for all game data and global settings.

```sql
CREATE TABLE world (
    id INTEGER PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    created_date TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_played_date TIMESTAMPTZ,
    current_season INTEGER NOT NULL DEFAULT 1,
    current_week INTEGER NOT NULL DEFAULT 1,
    start_year INTEGER NOT NULL,
    global_growth_rate DECIMAL(4,3) NOT NULL DEFAULT 1.000,
    sim_speed_factor DECIMAL(3,2) NOT NULL DEFAULT 1.00,
    randomness_factor DECIMAL(3,2) NOT NULL DEFAULT 1.00
);
```

*(Note: I have updated DATETIME to TIMESTAMPTZ for better timezone handling in Postgres, reflecting the `user.preferred_timezone` field)*

#### Constraint/Relationship notes

- **Primary Key**: `id` - Single record per game instance
- **Global Growth Rate**: Base economic growth rate applied worldwide (1.000 = no growth, 1.020 = 2% growth)
- **Simulation Settings**:
  - `sim_speed_factor`: Multiplier for adjusting simulation speed (e.g., for UI).
  - `randomness_factor`: A global modifier for random events in the simulation.
- **Economy Modifiers**: Will be stored in separate `world_economy_modifiers` table to allow flexible configuration
- **Global Settings**: Additional game-wide settings will be stored in separate `world_settings` table for extensibility

### Relationship Model

The Relationship table is a generic, polymorphic model designed to track the strength and type of connection between any two entities in the game. This can represent person-to-person friendships, team rivalries, player-to-team loyalty, etc.

```sql
CREATE TABLE relationship (
    entity1_id INTEGER NOT NULL,
    entity1_type VARCHAR(50) NOT NULL,
    entity2_id INTEGER NOT NULL,
    entity2_type VARCHAR(50) NOT NULL,
    relationship_type VARCHAR(50) NOT NULL,
    strength INTEGER NOT NULL DEFAULT 0,
    created_date TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_date TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (entity1_id, entity1_type, entity2_id, entity2_type, relationship_type)
);
```

#### Constraint/Relationship notes

- **Composite Primary Key**: Ensures a unique relationship type between any two specific entities.
- **Polymorphic Design**:
  - `entity1_id` / `entity2_id`: The ID of the entity.
  - `entity1_type` / `entity2_type`: The name of the table the ID belongs to (e.g., 'individual', 'team', 'league').
- **Relationship Type**: A descriptor for the relationship (e.g., 'friendship', 'rivalry', 'familial_sibling', 'employment').
- **Strength**: A numeric counter representing the quality of the relationship. Can be positive or negative (e.g., -100 to 100).
- **Direction**: For non-symmetrical relationships (like 'employment'), `entity1` can be considered the source/subject and `entity2` the target/object. For symmetrical relationships ('friendship'), the order is not significant, and applications should check for both permutations if necessary.

---

## User & Access Control Models

These models contain all tables related to the application users and their permissions.

### User Model

The User table stores basic information and preferences for players of the game.

```sql
CREATE TABLE "user" (
    id INTEGER PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    display_name VARCHAR(100),
    created_date TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_login_date TIMESTAMPTZ,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    preferred_timezone VARCHAR(50) DEFAULT 'UTC'
);
```

*(Note: `user` is a reserved keyword in SQL, so it's quoted as `"user"` for safety. DATETIME changed to TIMESTAMPTZ.)*

#### Constraint/Relationship notes

- **Primary Key**: `id` - Unique identifier for each user
- **Unique Constraints**: `username` and `email` must be unique across all users
- **Authentication**: Password stored as hash for security
- **Display Name**: Optional friendly name for UI display
- **Activity Tracking**: Last login and active status for user management
- **Timezone**: User's preferred timezone for date/time display
- **Future Expansion**: Additional user preferences and settings will be stored in separate `user_preferences` table
- **Game Characters**: User's in-game characters/personas will be stored in separate `character` table linking to this user

### Role Model

The Role table defines a standardized list of permission levels a user can have within a game world.

```sql
CREATE TABLE role (
    id INTEGER PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT
);
```

#### Constraint/Relationship notes

- **Primary Key**: `id` - Unique identifier for the role.
- `name`: The machine-friendly name of the role (e.g., 'commissioner', 'owner', 'observer').
- `description`: A user-friendly explanation of what the role entails.

### User World Role Model

This table links a `user` to a `world` and assigns them a specific `role`, creating a proper relationship. This is crucial for managing permissions, such as who can advance the simulation clock or edit league parameters.

```sql
CREATE TABLE user_world_role (
    user_id INTEGER NOT NULL,
    world_id INTEGER NOT NULL,
    role_id INTEGER NOT NULL,
    character_individual_id INTEGER,
    PRIMARY KEY (user_id, world_id),
    FOREIGN KEY (user_id) REFERENCES "user"(id) ON DELETE CASCADE,
    FOREIGN KEY (world_id) REFERENCES world(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES role(id),
    FOREIGN KEY (character_individual_id) REFERENCES individual(id) ON DELETE SET NULL
);
```

#### Constraint/Relationship notes

- **Composite Primary Key**: Ensures a user has only one primary role per world.
- **Foreign Keys**: Links the user account, game world, and the user's specific role for that world.
- `character_individual_id`: If the user has a playable character in this world (like an owner or GM), this links to their corresponding record in the `individual` table.

---

## Geographic Models

### Country Model

The Country table represents different nations in the game world. Each country has economic, climate, and cultural characteristics that affect gameplay within that region.

```sql
CREATE TABLE country (
    pk INTEGER PRIMARY KEY,
    world_pk INTEGER NOT NULL,
    name VARCHAR(100) NOT NULL UNIQUE,
    code VARCHAR(3) NOT NULL UNIQUE,
    wealth_level DECIMAL(5,2) NOT NULL DEFAULT 1.00,
    avg_temperature INTEGER NOT NULL,
    football_interest_modifier DECIMAL(3,2) NOT NULL DEFAULT 1.00,
    economic_stability DECIMAL(3,2) NOT NULL DEFAULT 1.00,
    population INTEGER,
    currency_code VARCHAR(3) NOT NULL DEFAULT 'USD',
    growth_rate DECIMAL(4,3) NOT NULL DEFAULT 1.000,
    created_date TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (world_id) REFERENCES world(id) ON DELETE CASCADE
);
```

#### Constraint/Relationship notes

- **Primary Key**: `pk` - Unique identifier for each country
- **Foreign Key**: `world_pk` - Links to the world this country exists in
- **Unique Constraints**: `name` and `code` must be unique within a world
- **Wealth Level**: Multiplier affecting economic activities (1.00 = baseline, >1.00 = wealthier, <1.00 = poorer)
- **Temperature**: Average temperature in Fahrenheit, affects player preferences and costs
- **Football Interest**: Modifier for how popular football is in this country (affects attendance, media coverage, etc.)
- **Economic Stability**: Affects contract negotiations, sponsorship deals, and market volatility
- **Population**: Total population, affects potential fan base and player pool
- **Currency**: Local currency code for economic calculations
- **Growth Rate**: Annual economic growth rate modifier, which combines with the `world.global_growth_rate`.
- **Growth Rate Calculation**: The effective growth rate for a country is calculated by combining the world's global growth rate with the country's growth rate modifier (e.g., `effective_country_growth = world.global_growth_rate * country.growth_rate`).
- **Additional Modifiers**: Country-specific gameplay modifiers will be stored in separate `country_modifiers` table for flexibility

### State Model

The State table represents states/provinces within countries. Each state has its own economic and cultural characteristics that modify the base country values, creating regional variation within nations.

```sql
CREATE TABLE state (
    pk INTEGER PRIMARY KEY,
    country_pk INTEGER NOT NULL,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(10) NOT NULL,
    wealth_modifier DECIMAL(3,2) NOT NULL DEFAULT 1.00,
    football_interest_modifier DECIMAL(3,2) NOT NULL DEFAULT 1.00,
    economic_stability_modifier DECIMAL(3,2) NOT NULL DEFAULT 1.00,
    population INTEGER,
    avg_temperature_modifier INTEGER NOT NULL DEFAULT 0,
    tax_rate DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    growth_rate DECIMAL(4,3) NOT NULL DEFAULT 1.000,
    created_date TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (country_id) REFERENCES country(id) ON DELETE CASCADE,
    UNIQUE(country_id, name),
    UNIQUE(country_id, code)
);
```

#### Constraint/Relationship notes

- **Primary Key**: `pk` - Unique identifier for each state
- **Foreign Key**: `country_pk` - Links to the country this state belongs to
- **Unique Constraints**: `name` and `code` must be unique within each country
- **Wealth Modifier**: Multiplier applied to country's wealth level (1.00 = same as country, >1.00 = wealthier than country average)
- **Football Interest Modifier**: Applied to country's football interest (stacks with country modifier)
- **Economic Stability Modifier**: Applied to country's economic stability
- **Temperature Modifier**: Added/subtracted from country's average temperature (in Fahrenheit)
- **Tax Rate**: State-specific tax rate affecting team finances and player salaries
- **Population**: State population, affects local market size
- **Growth Rate**: Annual economic growth rate modifier, which combines with the effective country growth rate.
- **Growth Rate Calculation**: The effective growth rate for a state is calculated by combining the effective country growth rate with the state's growth rate modifier (e.g., `effective_state_growth = effective_country_growth * state.growth_rate`).
- **Modifier Inheritance**: Team calculations will use: `country_value * country_modifier * state_modifier`
- **Additional Modifiers**: State-specific gameplay modifiers will be stored in separate `state_modifiers` table for flexibility

### City Model

The City table represents cities within states. Each city has its own economic and cultural characteristics that modify the base state and country values, creating local variation within regions.

```sql
CREATE TABLE city (
    pk INTEGER PRIMARY KEY,
    state_pk INTEGER NOT NULL,
    name VARCHAR(100) NOT NULL,
    wealth_modifier DECIMAL(3,2) NOT NULL DEFAULT 1.00,
    football_interest_modifier DECIMAL(3,2) NOT NULL DEFAULT 1.00,
    economic_stability_modifier DECIMAL(3,2) NOT NULL DEFAULT 1.00,
    population INTEGER NOT NULL,
    avg_temperature_modifier INTEGER NOT NULL DEFAULT 0,
    cost_of_living_modifier DECIMAL(3,2) NOT NULL DEFAULT 1.00,
    growth_rate DECIMAL(4,3) NOT NULL DEFAULT 1.000,
    market_size VARCHAR(20) NOT NULL DEFAULT 'medium',
    created_date TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (state_id) REFERENCES state(id) ON DELETE CASCADE,
    UNIQUE(state_id, name)
);
```

#### Constraint/Relationship notes

- **Primary Key**: `pk` - Unique identifier for each city
- **Foreign Key**: `state_pk` - Links to the state this city belongs to
- **Unique Constraints**: `name` must be unique within each state
- **Wealth Modifier**: Multiplier applied to state's wealth level (1.00 = same as state, >1.00 = wealthier than state average)
- **Football Interest Modifier**: Applied to state's football interest (stacks with country and state modifiers)
- **Economic Stability Modifier**: Applied to state's economic stability
- **Temperature Modifier**: Added/subtracted from state's average temperature (in Fahrenheit)
- **Cost of Living**: Affects player salary expectations and team operational costs
- **Growth Rate**: Annual economic growth rate modifier, which combines with the effective state growth rate.
- **Market Size**: Categorical size ('small', 'medium', 'large', 'major') affecting revenue potential
- **Population**: City population, affects local fan base and media market
- **Modifier Inheritance**: Team calculations will use: `country_value * country_modifier * state_modifier * city_modifier`
- **Growth Rate Calculation**: The effective growth rate for a city is calculated by combining the effective state growth rate with the city's growth rate modifier. This cascading calculation (`world_growth * country_growth * state_growth * city_growth`) determines the local economic evolution.
- **Additional Modifiers**: City-specific gameplay modifiers will be stored in separate `city_modifiers` table for flexibility

---

## League & Competition Models

### League Model

The League table represents football leagues that can span multiple countries. Each league has its own rules, structure, and prestige level that affects gameplay.

```sql
CREATE TABLE league (
    id INTEGER PRIMARY KEY,
    world_id INTEGER NOT NULL,
    name VARCHAR(100) NOT NULL,
    short_name VARCHAR(20) NOT NULL,
    prestige_rating INTEGER NOT NULL DEFAULT 50,
    is_professional BOOLEAN NOT NULL DEFAULT TRUE,
    salary_cap DECIMAL(12,2),
    min_teams INTEGER NOT NULL DEFAULT 8,
    max_teams INTEGER NOT NULL DEFAULT 32,
    season_length INTEGER NOT NULL DEFAULT 16,
    playoff_format VARCHAR(50) NOT NULL DEFAULT 'single_elimination',
    has_conferences BOOLEAN NOT NULL DEFAULT FALSE,
    has_divisions BOOLEAN NOT NULL DEFAULT FALSE,
    enforce_geographic_rules BOOLEAN NOT NULL DEFAULT FALSE,
    created_date TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    FOREIGN KEY (world_id) REFERENCES world(id) ON DELETE CASCADE
);
```

#### Supporting Tables

##### League Countries (Many-to-Many)

```sql
CREATE TABLE league_country (
    league_id INTEGER NOT NULL,
    country_id INTEGER NOT NULL,
    is_primary_country BOOLEAN NOT NULL DEFAULT FALSE,
    created_date TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (league_id, country_id),
    FOREIGN KEY (league_id) REFERENCES league(id) ON DELETE CASCADE,
    FOREIGN KEY (country_id) REFERENCES country(id) ON DELETE CASCADE
);
```

##### League Membership Rules

```sql
CREATE TABLE league_membership_rule (
    id INTEGER PRIMARY KEY,
    league_id INTEGER NOT NULL,
    rule_type VARCHAR(50) NOT NULL,
    rule_value VARCHAR(255) NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_date TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (league_id) REFERENCES league(id) ON DELETE CASCADE
);
```

##### League Conferences

```sql
CREATE TABLE league_conference (
    id INTEGER PRIMARY KEY,
    league_id INTEGER NOT NULL,
    name VARCHAR(100) NOT NULL,
    short_name VARCHAR(20) NOT NULL,
    display_order INTEGER NOT NULL DEFAULT 1,
    created_date TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (league_id) REFERENCES league(id) ON DELETE CASCADE,
    UNIQUE(league_id, name),
    UNIQUE(league_id, short_name)
);
```

##### League Divisions

```sql
CREATE TABLE league_division (
    id INTEGER PRIMARY KEY,
    league_id INTEGER NOT NULL,
    conference_id INTEGER,
    name VARCHAR(100) NOT NULL,
    short_name VARCHAR(20) NOT NULL,
    display_order INTEGER NOT NULL DEFAULT 1,
    created_date TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (league_id) REFERENCES league(id) ON DELETE CASCADE,
    FOREIGN KEY (conference_id) REFERENCES league_conference(id) ON DELETE CASCADE,
    UNIQUE(league_id, name),
    UNIQUE(league_id, short_name)
);
```

##### Playoff Formats

```sql
CREATE TABLE playoff_format (
    id INTEGER PRIMARY KEY,
    format_code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    teams_qualify INTEGER NOT NULL,
    rounds INTEGER NOT NULL,
    created_date TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);
```

#### Constraint/Relationship notes

- **Primary Key**: `id` - Unique identifier for each league
- **Foreign Key**: `world_id` - Links to the world this league exists in
- **Prestige Rating**: 1-100 scale affecting player desirability and media attention
- **Professional Status**: Determines if players receive compensation
- **Salary Cap**: Optional salary limit for professional leagues
- **Team Limits**: Minimum and maximum number of teams allowed
- **Structure Options**: Conferences and divisions can be enabled independently
- **Geographic Rules**: When enabled, restricts team locations based on league countries
- **League Countries**: Many-to-many relationship allowing leagues to span multiple countries
- **Primary Country**: One country can be marked as the league's primary base
- **Membership Rules**: Flexible rule system for team eligibility
  - Rule types: 'geographic_restriction', 'stadium_capacity', 'financial_requirement', etc.
  - Rule values: JSON or string values defining specific requirements
- **League Conferences**: Optional organizational structure within leagues
  - Conferences can exist without divisions
  - Display order determines UI presentation
- **League Divisions**: Optional sub-structure within conferences or leagues
  - Can belong to a conference or directly to a league (conference_id nullable)
  - Display order determines UI presentation
- **Playoff Formats**: Standardized playoff structures
  - Format codes: 'single_elimination', 'double_elimination', 'round_robin', 'bracket_8', 'bracket_16', etc.
  - Teams qualify: Number of teams that make playoffs
  - Rounds: Number of playoff rounds
- **Additional Rules**: League-specific gameplay rules will be stored in separate `league_rules` table for extensibility

### League Standings Model

Tracks the performance of each team within a specific league for a given season. This is the source for playoff qualification.

```sql
CREATE TABLE league_standings (
    id INTEGER PRIMARY KEY,
    season_year INTEGER NOT NULL,
    league_id INTEGER NOT NULL,
    team_id INTEGER NOT NULL,
    wins INTEGER NOT NULL DEFAULT 0,
    losses INTEGER NOT NULL DEFAULT 0,
    ties INTEGER NOT NULL DEFAULT 0,
    points_for INTEGER NOT NULL DEFAULT 0,
    points_against INTEGER NOT NULL DEFAULT 0,
    UNIQUE(season_year, league_id, team_id),
    FOREIGN KEY (league_id) REFERENCES league(id) ON DELETE CASCADE,
    FOREIGN KEY (team_id) REFERENCES team(id) ON DELETE CASCADE
);
```

---

## Team & Organization Models

### Team Model

The Team table represents football teams that can participate in multiple leagues. Each team has location, branding, and organizational attributes.

```sql
CREATE TABLE team (
    id INTEGER PRIMARY KEY,
    world_id INTEGER NOT NULL,
    name VARCHAR(100) NOT NULL,
    short_name VARCHAR(20) NOT NULL,
    city_id INTEGER NOT NULL,
    owner_staff_id INTEGER,
    head_coach_staff_id INTEGER,
    current_playbook_id INTEGER,
    founded_year INTEGER,
    primary_color VARCHAR(7) NOT NULL,
    secondary_color VARCHAR(7) NOT NULL,
    mascot_name VARCHAR(50),
    budget DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_date TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (world_id) REFERENCES world(id) ON DELETE CASCADE,
    FOREIGN KEY (city_id) REFERENCES city(id) ON DELETE RESTRICT,
    FOREIGN KEY (owner_staff_id) REFERENCES staff(id) ON DELETE SET NULL,
    FOREIGN KEY (head_coach_staff_id) REFERENCES staff(id) ON DELETE SET NULL,
    FOREIGN KEY (current_playbook_id) REFERENCES playbook(id) ON DELETE SET NULL
);
```

#### Supporting Tables

##### Team League Memberships (Many-to-Many)

```sql
CREATE TABLE team_league (
    team_id INTEGER NOT NULL,
    league_id INTEGER NOT NULL,
    conference_id INTEGER,
    division_id INTEGER,
    joined_date TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    PRIMARY KEY (team_id, league_id),
    FOREIGN KEY (team_id) REFERENCES team(id) ON DELETE CASCADE,
    FOREIGN KEY (league_id) REFERENCES league(id) ON DELETE CASCADE,
    FOREIGN KEY (conference_id) REFERENCES league_conference(id) ON DELETE SET NULL,
    FOREIGN KEY (division_id) REFERENCES league_division(id) ON DELETE SET NULL
);
```

##### Team Artwork

```sql
CREATE TABLE team_artwork (
    id INTEGER PRIMARY KEY,
    team_id INTEGER NOT NULL,
    artwork_type VARCHAR(50) NOT NULL,
    file_url VARCHAR(255) NOT NULL,
    description TEXT,
    is_primary BOOLEAN NOT NULL DEFAULT FALSE,
    created_date TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (team_id) REFERENCES team(id) ON DELETE CASCADE
);
```

##### Team Roster
(Manages which players are on the team and their current status)
```sql
CREATE TABLE team_roster (
    id INTEGER PRIMARY KEY,
    team_id INTEGER NOT NULL,
    player_id INTEGER NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'active', -- e.g., 'active', 'injured_reserve'
    UNIQUE(team_id, player_id),
    FOREIGN KEY (team_id) REFERENCES team(id) ON DELETE CASCADE,
    FOREIGN KEY (player_id) REFERENCES player(id) ON DELETE CASCADE
);
```

##### Depth Charts
(Defines the strategic packages or formations a team uses)
```sql
CREATE TABLE depth_chart (
    id INTEGER PRIMARY KEY,
    team_id INTEGER NOT NULL,
    name VARCHAR(100) NOT NULL, -- e.g., "Base Offense", "Nickel Defense"
    is_default BOOLEAN NOT NULL DEFAULT FALSE,
    FOREIGN KEY (team_id) REFERENCES team(id) ON DELETE CASCADE,
    UNIQUE(team_id, name)
);
```

##### Depth Chart Positions
(Assigns players from the roster to specific positions and depths within a strategic package)
```sql
CREATE TABLE depth_chart_position (
    id INTEGER PRIMARY KEY,
    depth_chart_id INTEGER NOT NULL,
    player_id INTEGER NOT NULL,
    position_id INTEGER NOT NULL,
    depth_order INTEGER NOT NULL,
    FOREIGN KEY (depth_chart_id) REFERENCES depth_chart(id) ON DELETE CASCADE,
    FOREIGN KEY (player_id) REFERENCES player(id) ON DELETE CASCADE,
    FOREIGN KEY (position_id) REFERENCES player_position(id) ON DELETE RESTRICT,
    UNIQUE(depth_chart_id, position_id, depth_order)
);
```

#### Constraint/Relationship notes

- **Primary Key**: `id` - Unique identifier for each team
- **Foreign Keys**:
  - `world_id` - Links to the world this team exists in
  - `city_id` - Team's home city location (RESTRICT prevents deletion of cities with teams)
- **Team Identity**: Name, colors, mascot, and logo define team branding
- **Leadership**: The `owner_staff_id` and `head_coach_staff_id` fields link directly to the `staff` table. This provides a formal connection to the individuals filling these roles, allowing the simulation to access their attributes and personalities.
- **Multi-League Membership**: Teams can participate in multiple leagues simultaneously
- **League Structure**: Teams can be assigned to specific conferences/divisions within each league
- **Team Artwork**: Flexible system for storing multiple artwork types
  - Artwork types: 'logo', 'helmet', 'jersey_home', 'jersey_away', 'banner', 'mascot_image'
  - Primary flag indicates main artwork for each type
- **Budget**: Team's available funds for operations and player salaries
- **Geographic Inheritance**: Teams inherit economic and cultural modifiers from their city location
- **Stadium Relationships**: Teams can play in multiple stadiums - relationships will be managed through separate `stadium` and `team_stadium` tables
- **Future Expansion**: Team facilities, staff, and historical records will be stored in separate tables

### Playbook Model

Represents a collection of plays that a team can use.

```sql
CREATE TABLE playbook (
    id INTEGER PRIMARY KEY,
    team_id INTEGER,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    is_default BOOLEAN NOT NULL DEFAULT FALSE,
    FOREIGN KEY (team_id) REFERENCES team(id) ON DELETE SET NULL
);
```

#### Constraint/Relationship notes
- `team_id`: The team this playbook belongs to. Can be NULL for default, system-wide playbooks.
- `is_default`: Flags playbooks that are available to all teams.

### Play Design Model

Defines the abstract concept of a single football play.

```sql
CREATE TABLE play_design (
    id INTEGER PRIMARY KEY,
    playbook_id INTEGER NOT NULL,
    name VARCHAR(100) NOT NULL,
    play_type VARCHAR(50) NOT NULL,
    formation VARCHAR(50),
    base_description TEXT,
    FOREIGN KEY (playbook_id) REFERENCES playbook(id) ON DELETE CASCADE
);
```

#### Constraint/Relationship notes
- `play_type`: Categorical type (e.g., 'RUSH', 'PASS', 'PUNT', 'FIELD_GOAL').
- `base_description`: A template for the text log, e.g., "{QB_NAME} hands off to {RB_NAME}...".

### Stadium Model

The Stadium table represents the physical venues where games are played. Each stadium has a location, capacity, and specific characteristics that can affect gameplay.

```sql
CREATE TABLE stadium (
    id INTEGER PRIMARY KEY,
    world_id INTEGER NOT NULL,
    city_id INTEGER NOT NULL,
    name VARCHAR(100) NOT NULL,
    capacity INTEGER NOT NULL,
    year_built INTEGER,
    stadium_type VARCHAR(20) NOT NULL DEFAULT 'open_air',
    playing_surface VARCHAR(20) NOT NULL DEFAULT 'grass',
    created_date TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (world_id) REFERENCES world(id) ON DELETE CASCADE,
    FOREIGN KEY (city_id) REFERENCES city(id) ON DELETE RESTRICT
);
```

#### Constraint/Relationship notes

- **Primary Key**: `id` - Unique identifier for each stadium.
- **Foreign Keys**:
  - `world_id` - Links the stadium to the world it exists in.
  - `city_id` - The stadium's home city, which also links it to the cascading modifier system. RESTRICT prevents deleting a city that has a stadium.
- **Attributes**:
  - `stadium_type`: Categorical type of stadium (e.g., 'open_air', 'retractable_roof', 'dome').
  - `playing_surface`: The type of field turf (e.g., 'grass', 'artificial_turf', 'hybrid').
- **Team Relationship**: A team's home stadium will be defined through a separate `team_stadium` relationship table.

---

## Entity Generation Models

These models provide a flexible, data-driven framework for generating new entities like players. This system uses archetypes to define templates and configurations to control their spawn rates over time and across different contexts (e.g., for a specific league or country).

### Archetype Model

The Archetype table defines templates for different types of entities. For players, this could be 'Pocket Passer QB' or 'Coverage Safety'.

```sql
CREATE TABLE archetype (
    id INTEGER PRIMARY KEY,
    entity_type VARCHAR(50) NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_date TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(entity_type, name)
);
```

#### Supporting Tables

##### Archetype Attributes

```sql
CREATE TABLE archetype_attribute (
    id INTEGER PRIMARY KEY,
    archetype_id INTEGER NOT NULL,
    attribute_name VARCHAR(50) NOT NULL,
    base_value INTEGER NOT NULL,
    FOREIGN KEY (archetype_id) REFERENCES archetype(id) ON DELETE CASCADE,
    UNIQUE(archetype_id, attribute_name)
);
```

#### Constraint/Relationship notes

- **Primary Key**: `id` - Unique identifier for each archetype.
- `entity_type`: The kind of entity this archetype defines (e.g., 'player', 'coach').
- `archetype_attribute`: This table stores the baseline values for all relevant attributes of an archetype (e.g., for a player archetype, `attribute_name` could be 'speed' or 'strength'). The generation logic will use this `base_value` and apply randomization to create unique individuals.

### Generation Configuration Model

This polymorphic table defines how frequently different archetypes should be generated for a given scope (e.g., a league, a country, or globally). This allows generation logic to change over time and by region.

```sql
CREATE TABLE generation_config (
    id INTEGER PRIMARY KEY,
    config_scope VARCHAR(50) NOT NULL,
    scope_id INTEGER NOT NULL,
    archetype_id INTEGER NOT NULL,
    spawn_weight INTEGER NOT NULL DEFAULT 100,
    start_year INTEGER NOT NULL,
    end_year INTEGER,
    created_date TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (archetype_id) REFERENCES archetype(id) ON DELETE CASCADE
);
```

#### Constraint/Relationship notes

- **Polymorphic Scope**:
  - `config_scope`: Defines the context for this configuration (e.g., 'world', 'league', 'country').
  - `scope_id`: The ID of the record in the table defined by `config_scope`.
- `archetype_id`: The archetype this configuration applies to.
- `spawn_weight`: A relative weight determining the probability of this archetype being generated within its scope. Higher values are more common.
- `start_year` / `end_year`: Defines the range of seasons (inclusive) during which this configuration is active, allowing for player generation to evolve over time. `end_year` can be NULL for an indefinite period.

---

## People & Character Models

### Individual Model

The Individual table is the base representation for any person in the game world, including players, coaches, owners, and staff. It stores common personal information and personality traits.

```sql

CREATE TABLE individual (
    pk INTEGER PRIMARY KEY,
    world_pk INTEGER NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    birth_date DATE NOT NULL,
    home_city_pk INTEGER NOT NULL,
    desire_to_win INTEGER NOT NULL DEFAULT 50,
    greed INTEGER NOT NULL DEFAULT 50,
    demeanor INTEGER NOT NULL DEFAULT 50,
    luck INTEGER NOT NULL DEFAULT 50,
    loyalty INTEGER NOT NULL DEFAULT 50,
    patience INTEGER NOT NULL DEFAULT 50,
    likability INTEGER NOT NULL DEFAULT 50,
    is_draft_eligible BOOLEAN NOT NULL DEFAULT FALSE,
    draft_year INTEGER,
    created_date TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (world_id) REFERENCES world(id) ON DELETE CASCADE,
    FOREIGN KEY (home_city_id) REFERENCES city(id) ON DELETE RESTRICT
);
```

#### Constraint/Relationship notes

- **Primary Key**: `id` - Unique identifier for each individual.
- **Foreign Keys**:
  - `world_id` - Links the individual to a specific game world.
  - `home_city_id` - The individual's home city, which determines their home state and country.
- **Personality Ratings**: Scored 1-100, influencing decisions and interactions.
  - `desire_to_win`: Competitiveness.
  - `greed`: Focus on financial gain.
  - `demeanor`: Public and private temperament.
  - `luck`: A general modifier for random events.
  - `loyalty`: Willingness to stay with current teams, friends, etc.
  - `patience`: Tolerance for lack of success or development.
- **Relationships**: The generic `relationship` table manages connections between entities, including individual-to-individual relationships like family, friendships, or rivalries.
- **Character Roles**: This table serves as a base, with specific roles like Player or Coach defined in other tables that link back to this one.
- **Draft Status**: `is_draft_eligible` and `draft_year` manage a player's entry into the league draft pool.

### Player Position Model

This table is a simple lookup that defines all valid player positions in the game, ensuring standardization.

```sql
CREATE TABLE player_position (
    pk INTEGER PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    abbreviation VARCHAR(5) NOT NULL UNIQUE,
    unit VARCHAR(10) NOT NULL -- e.g., 'QB', 'RB', 'WR', 'OL', 'DL', 'LB', 'DB', 'K', 'P'
);
```

#### Constraint/Relationship notes
- `position_group`: A categorical assignment for easier logic filtering and AI substitutions (e.g., 'QB', 'RB', 'WR', 'OL', 'DL', 'LB', 'DB', 'K', 'P').

### Player Model

The Player table contains all athletic attributes and game-specific information for an individual who is a football player. It links to an `Individual` record for personal details.

```sql
CREATE TABLE player (
    pk INTEGER PRIMARY KEY,
    individual_pk INTEGER NOT NULL UNIQUE,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    status VARCHAR(50) NOT NULL DEFAULT 'free_agent',
    speed INTEGER NOT NULL,
    explosiveness INTEGER NOT NULL,
    strength INTEGER NOT NULL,
    stamina INTEGER NOT NULL,
    catching INTEGER NOT NULL,
    ball_security INTEGER NOT NULL,
    footwork INTEGER NOT NULL,
    breaking_tackles INTEGER NOT NULL,
    anticipation INTEGER NOT NULL,
    blocking_power INTEGER NOT NULL,
    blocking_technique INTEGER NOT NULL,
    shed_blocks INTEGER NOT NULL,
    tackling INTEGER NOT NULL,
    teamwork INTEGER NOT NULL,
    communication INTEGER NOT NULL,
    created_date TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (individual_id) REFERENCES individual(id) ON DELETE CASCADE
);
```

#### Supporting Tables

##### Player Stats History

```sql
CREATE TABLE player_stats_history (
    pk INTEGER PRIMARY KEY,
    player_pk INTEGER NOT NULL,
    season_year INTEGER NOT NULL,
    level VARCHAR(50) NOT NULL,
    team_name VARCHAR(100),
    -- Statistical fields for all positions will be columns here
    created_date TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (player_id) REFERENCES player(id) ON DELETE CASCADE
);
```

#### Constraint/Relationship notes

- **Primary Key**: `id` - Unique identifier for each player record.
- **Foreign Key**: `individual_id` - A one-to-one link to the `Individual` table for personal data.
- **Status**: Player's current status (e.g., 'free_agent', 'on_roster', 'injured', 'retired').
- **Athletic Ratings**: Scored 1-100, representing the player's skills on the field.
- **Team Relationship**: A player's contractual relationship with a team is managed through the `player_contract` and `player_contract_year` tables.
- **Statistical History**: The `player_stats_history` table tracks a player's performance at various levels (e.g., 'high_school', 'college', 'pro') throughout their career.


---

## Personnel, Contracts & Staff Models

This section contains every table related to contracts, salaries, and employment for both players and staff. This makes the financial and team management aspects of the schema much easier to understand as a single system.

### Player Contract Model

The Player Contract table holds the overarching terms of an agreement between a player and a team, including duration, options, and special clauses.

```sql
CREATE TABLE player_contract (
    id INTEGER PRIMARY KEY,
    player_id INTEGER NOT NULL,
    team_id INTEGER NOT NULL,
    start_year INTEGER NOT NULL,
    total_years INTEGER NOT NULL,
    no_trade_clause BOOLEAN NOT NULL DEFAULT FALSE,
    team_option_year INTEGER,
    player_option_year INTEGER,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    FOREIGN KEY (player_id) REFERENCES player(id) ON DELETE CASCADE,
    FOREIGN KEY (team_id) REFERENCES team(id) ON DELETE CASCADE
);
```

#### Constraint/Relationship notes

- **Links**: Connects a `player` to a `team`.
- **Duration**: `start_year` and `total_years` define the guaranteed length of the contract.
- `no_trade_clause`: A boolean flag to prevent the player from being traded.
- **Options**: `team_option_year` and `player_option_year` are nullable integers. If a value is present, it indicates the season in which a team or player option can be exercised to extend the contract.
- `is_active`: Flags the player's current contract. A player can have historical (inactive) contracts but only one active one.

### Player Contract Year Model

This table stores the year-by-year financial details for a specific contract, allowing for salary structures that change over the life of the deal.

```sql
CREATE TABLE player_contract_year (
    id INTEGER PRIMARY KEY,
    player_contract_id INTEGER NOT NULL,
    season_year INTEGER NOT NULL,
    base_salary_usd DECIMAL(12,2) NOT NULL,
    signing_bonus_proration_usd DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    performance_bonus_usd DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    performance_bonus_criteria TEXT,
    is_guaranteed BOOLEAN NOT NULL DEFAULT TRUE,
    UNIQUE (player_contract_id, season_year),
    FOREIGN KEY (player_contract_id) REFERENCES player_contract(id) ON DELETE CASCADE
);
```

#### Constraint/Relationship notes

- **Foreign Key**: Links each yearly breakdown to a master `player_contract`.
- **Unique Constraint**: Ensures there is only one financial record per year for any given contract.
- **Financials**: Defines the player's compensation for that specific `season_year`.
- `performance_bonus_criteria`: A text field describing the conditions that must be met for the bonus to be earned (e.g., "1200+ rushing yards", "Pro Bowl selection"). This allows for flexible, readable bonus terms.

### Staff Role Type Model

This is a lookup table that defines all possible job roles in the game world, both for team personnel and personal staff.

```sql
CREATE TABLE staff_role_type (
    id INTEGER PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    category VARCHAR(50) NOT NULL,
    description TEXT
);
```

#### Constraint/Relationship notes

- `name`: The user-facing name of the role (e.g., 'Head Coach', 'Financial Advisor').
- `category`: A broader grouping for logic and UI (e.g., 'Coaching Staff', 'Medical Staff', 'Personal Finance', 'Personal Legal').

### Staff Model

The Staff table is the base for any non-player individual who is employed. It links an `individual` to their employer, which can be either a `team` or another `individual`. It holds universal employment data like salary and contract terms.

```sql
CREATE TABLE staff (
    id INTEGER PRIMARY KEY,
    individual_id INTEGER NOT NULL UNIQUE,
    staff_role_type_id INTEGER NOT NULL,
    employer_team_id INTEGER,
    employer_individual_id INTEGER,
    role_title VARCHAR(100) NOT NULL,
    salary_usd DECIMAL(12,2) NOT NULL,
    contract_start_year INTEGER NOT NULL,
    contract_years INTEGER NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'active',
    FOREIGN KEY (individual_id) REFERENCES individual(id) ON DELETE CASCADE,
    FOREIGN KEY (staff_role_type_id) REFERENCES staff_role_type(id) ON DELETE RESTRICT,
    FOREIGN KEY (employer_team_id) REFERENCES team(id) ON DELETE SET NULL,
    FOREIGN KEY (employer_individual_id) REFERENCES individual(id) ON DELETE SET NULL,
    CONSTRAINT check_single_employer CHECK (
        (employer_team_id IS NOT NULL AND employer_individual_id IS NULL) OR
        (employer_team_id IS NULL AND employer_individual_id IS NOT NULL)
    )
);
```

#### Constraint/Relationship notes

- **Foreign Keys**: Links to the person (`individual_id`) and their job definition (`staff_role_type_id`).
- **Flexible Employer**: `employer_team_id` is for team staff. `employer_individual_id` is for personal staff.
- **`check_single_employer`**: This crucial database constraint ensures that a staff member must have exactly one employer—either a team OR an individual, but never both or neither.
- `role_title`: The specific title for this instance of the role (e.g. 'Offensive Coordinator' is a title for the 'Coach' role type).

### Financial Advisor Attributes Model

Stores attributes specific only to financial advisors.

```sql
CREATE TABLE financial_advisor_attributes (
    staff_id INTEGER PRIMARY KEY,
    investment_skill INTEGER NOT NULL DEFAULT 50,
    risk_tolerance_philosophy VARCHAR(100),
    specialty VARCHAR(100),
    FOREIGN KEY (staff_id) REFERENCES staff(id) ON DELETE CASCADE
);
```

### Legal Advisor Attributes Model

Stores attributes specific only to lawyers/agents.

```sql
CREATE TABLE legal_advisor_attributes (
    staff_id INTEGER PRIMARY KEY,
    contract_negotiation_skill INTEGER NOT NULL DEFAULT 50,
    litigation_skill INTEGER NOT NULL DEFAULT 50,
    bar_association_number VARCHAR(100),
    FOREIGN KEY (staff_id) REFERENCES staff(id) ON DELETE CASCADE
);
```

### Personal Wellness Attributes Model

A generic table for personal staff focused on the character's well-being, such as chefs or personal trainers.

```sql
CREATE TABLE personal_wellness_attributes (
    staff_id INTEGER PRIMARY KEY,
    effectiveness_skill INTEGER NOT NULL DEFAULT 50,
    specialty VARCHAR(100),
    FOREIGN KEY (staff_id) REFERENCES staff(id) ON DELETE CASCADE
);
```

### Coach Attributes Model

Stores attributes specific to coaches, influencing their AI behavior.

```sql
CREATE TABLE coach_attributes (
    staff_id INTEGER PRIMARY KEY,
    play_calling_skill INTEGER NOT NULL DEFAULT 50,
    risk_tolerance INTEGER NOT NULL DEFAULT 50,
    preferred_style VARCHAR(100),
    FOREIGN KEY (staff_id) REFERENCES staff(id) ON DELETE CASCADE
);
```

---

## Game & Simulation Models

These models define the structure for managing an active game and recording the results of individual play simulations. They form the link between the static team/player data and the dynamic simulation engine.

### Game Model

The Game table stores the static, high-level information about a single football game matchup, such as the teams involved, location, and date.

```sql
CREATE TABLE game (
    id INTEGER PRIMARY KEY,
    world_id INTEGER NOT NULL,
    home_team_id INTEGER NOT NULL,
    away_team_id INTEGER NOT NULL,
    stadium_id INTEGER NOT NULL,
    game_date TIMESTAMPTZ NOT NULL,
    season_year INTEGER NOT NULL,
    week_number INTEGER,
    game_slot INTEGER,
    status VARCHAR(20) NOT NULL DEFAULT 'scheduled',
    created_date TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (world_id) REFERENCES world(id) ON DELETE CASCADE,
    FOREIGN KEY (home_team_id) REFERENCES team(id) ON DELETE CASCADE,
    FOREIGN KEY (away_team_id) REFERENCES team(id) ON DELETE CASCADE,
    FOREIGN KEY (stadium_id) REFERENCES stadium(id) ON DELETE RESTRICT
);
```

#### Constraint/Relationship notes

- **Primary Key**: `id` - Unique identifier for each game.
- **Foreign Keys**:
  - `stadium_id` - Links the game to the `stadium` where it was played. RESTRICT prevents deleting a stadium if it has games associated with it.
- **Status**: The current state of the game (e.g., 'scheduled', 'in_progress', 'final').
- **State Separation**: The dynamic, play-by-play state of the game (score, clock, possession) is stored in the separate `game_state` table.

### Game State Model

This table tracks the dynamic state of a single game, which is updated after every play. It holds all the information that changes during the game, such as score, time, and ball position.

```sql
CREATE TABLE game_state (
    game_id INTEGER PRIMARY KEY,
    home_score INTEGER NOT NULL DEFAULT 0,
    away_score INTEGER NOT NULL DEFAULT 0,
    current_quarter INTEGER,
    time_remaining_in_quarter INTEGER,
    team_with_possession_id INTEGER,
    down INTEGER,
    distance INTEGER,
    ball_on_yard_line INTEGER,
    is_drive_active BOOLEAN NOT NULL DEFAULT FALSE,
    drive_start_yard_line INTEGER,
    drive_start_time_in_quarter INTEGER,
    FOREIGN KEY (game_id) REFERENCES game(id) ON DELETE CASCADE,
    FOREIGN KEY (team_with_possession_id) REFERENCES team(id) ON DELETE SET NULL
);
```

#### Constraint/Relationship notes
- **Primary Key**: `game_id` - This creates a one-to-one relationship with the `game` table.
- **Game State Fields**:
  - `time_remaining_in_quarter`: Measured in seconds.
  - `ball_on_yard_line`: Represents the position of the ball from the perspective of the offense's goal line (e.g., a value of 25 means the ball is on the offense's own 25-yard line).
- **Drive Tracking**: `is_drive_active`, `drive_start_yard_line`, and `drive_start_time_in_quarter` are used by the simulation engine to manage a single possession.
- **Note on the Field Model**: The `game_state` table stores the persistent state of a game *between* plays. The detailed, coordinate-based "Field Model" described in the architecture notes is a transient, in-memory data structure used by the simulation engine *during* the execution of a single play. The results are then captured in the `play` table and used to update this `game_state` record.

### Game Plan Model

Stores the high-level strategic instructions for a team for a specific game, influencing the AI coach's play-calling decisions.

```sql
CREATE TABLE game_plan (
    id INTEGER PRIMARY KEY,
    game_id INTEGER NOT NULL,
    team_id INTEGER NOT NULL,
    pass_run_ratio DECIMAL(3,2) NOT NULL DEFAULT 0.50, -- 0.0 = 100% run, 1.0 = 100% pass
    offensive_aggression INTEGER NOT NULL DEFAULT 50, -- 1-100 scale for 4th down attempts, trick plays
    defensive_aggression INTEGER NOT NULL DEFAULT 50, -- 1-100 scale for blitzing, all-out coverage
    UNIQUE(game_id, team_id),
    FOREIGN KEY (game_id) REFERENCES game(id) ON DELETE CASCADE,
    FOREIGN KEY (team_id) REFERENCES team(id) ON DELETE CASCADE
);
```

#### Constraint/Relationship notes

- **Unique Constraint**: Ensures each team has only one game plan per game.
- **Ratios & Scales**: These values provide high-level guidance to the simulation engine's AI.
  - `pass_run_ratio`: Governs the tendency to call pass plays vs. run plays.
  - `offensive_aggression`: Influences decisions like going for it on 4th down.
  - `defensive_aggression`: Influences the frequency of blitzes vs. conservative coverage.

### Coach Instruction Log Model

Logs the instructions a user gives to their AI coach during a game, providing a history of their strategic decisions.

```sql
CREATE TABLE coach_instruction_log (
    id INTEGER PRIMARY KEY,
    game_id INTEGER NOT NULL,
    team_id INTEGER NOT NULL,
    quarter INTEGER,
    play_number INTEGER,
    instruction_text TEXT NOT NULL,
    was_override BOOLEAN NOT NULL DEFAULT FALSE,
    created_date TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (game_id) REFERENCES game(id) ON DELETE CASCADE,
    FOREIGN KEY (team_id) REFERENCES team(id) ON DELETE CASCADE
);
```

### Play Model

The Play table serves as a historical log, recording the key details and outcome of every single play that occurs within a game.

```sql
CREATE TABLE play (
    id INTEGER PRIMARY KEY,
    game_id INTEGER NOT NULL,
    play_number INTEGER NOT NULL,
    offense_team_id INTEGER NOT NULL,
    defense_team_id INTEGER NOT NULL,
    offense_play_design_id INTEGER,
    defense_play_design_id INTEGER,
    quarter INTEGER NOT NULL,
    down INTEGER,
    distance INTEGER,
    start_yard_line INTEGER,
    end_yard_line INTEGER,
    result_type VARCHAR(50),
    yards_gained INTEGER NOT NULL,
    play_description TEXT,
    turnover_type VARCHAR(50),
    created_date TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (game_id) REFERENCES game(id) ON DELETE CASCADE,
    FOREIGN KEY (offense_team_id) REFERENCES team(id) ON DELETE CASCADE,
    FOREIGN KEY (defense_team_id) REFERENCES team(id) ON DELETE CASCADE,
    FOREIGN KEY (offense_play_design_id) REFERENCES play_design(id) ON DELETE SET NULL,
    FOREIGN KEY (defense_play_design_id) REFERENCES play_design(id) ON DELETE SET NULL
);
```

#### Constraint/Relationship notes

- **Primary Key**: `id` - Unique identifier for each play record.
- **Foreign Keys**: `game_id` links the play to the specific game it occurred in.
- **Play Calling**: The `offense_play_design_id` and `defense_play_design_id` fields link to the specific plays called by each team's AI, creating a crucial record of strategic decisions versus outcomes. These are nullable to account for plays without a formal design (e.g., penalties before the snap).
- **Play Log**: This table provides a complete, sequential record of a game's events, which can be used for box scores, recaps, and analysis.
- `result_type`: A categorical description of the play (e.g., 'RUSH', 'PASS_COMPLETE', 'PASS_INCOMPLETE', 'PUNT', 'FIELD_GOAL_MADE', 'PENALTY').
- `turnover_type`: Specifies the type of turnover, if any (e.g., 'INTERCEPTION', 'FUMBLE_LOST').

### Scoring Summary Model

A dedicated table to track every scoring play in a game. This provides an easy-to-query source for building a game's scoring summary without parsing the full play-by-play log.

```sql
CREATE TABLE scoring_summary (
    id INTEGER PRIMARY KEY,
    play_id INTEGER NOT NULL,
    game_id INTEGER NOT NULL,
    scoring_team_id INTEGER NOT NULL,
    quarter INTEGER NOT NULL,
    time_remaining_in_quarter INTEGER NOT NULL,
    points_scored INTEGER NOT NULL,
    scoring_play_type VARCHAR(50) NOT NULL,
    description TEXT,
    FOREIGN KEY (play_id) REFERENCES play(id) ON DELETE CASCADE,
    FOREIGN KEY (game_id) REFERENCES game(id) ON DELETE CASCADE,
    FOREIGN KEY (scoring_team_id) REFERENCES team(id) ON DELETE CASCADE
);
```

### Player Game Stats Model

Stores the aggregated statistics for a single player in a single game. This table is the source for box scores.

```sql
CREATE TABLE player_game_stats (
    id INTEGER PRIMARY KEY,
    game_id INTEGER NOT NULL,
    player_id INTEGER NOT NULL,
    team_id INTEGER NOT NULL,
    -- Passing Stats
    pass_attempts INTEGER DEFAULT 0,
    pass_completions INTEGER DEFAULT 0,
    pass_yards INTEGER DEFAULT 0,
    pass_touchdowns INTEGER DEFAULT 0,
    pass_interceptions INTEGER DEFAULT 0,
    -- Rushing Stats
    rush_attempts INTEGER DEFAULT 0,
    rush_yards INTEGER DEFAULT 0,
    rush_touchdowns INTEGER DEFAULT 0,
    -- Receiving Stats
    receptions INTEGER DEFAULT 0,
    receiving_yards INTEGER DEFAULT 0,
    receiving_touchdowns INTEGER DEFAULT 0,
    -- Defensive Stats
    tackles INTEGER DEFAULT 0,
    sacks DECIMAL(3,1) DEFAULT 0.0,
    interceptions_caught INTEGER DEFAULT 0,
    -- Special Teams Stats
    field_goals_made INTEGER DEFAULT 0,
    field_goals_attempted INTEGER DEFAULT 0,
    punts INTEGER DEFAULT 0,
    punt_yards INTEGER DEFAULT 0,
    -- Return Stats
    kick_returns INTEGER DEFAULT 0,
    kick_return_yards INTEGER DEFAULT 0,
    kick_return_touchdowns INTEGER DEFAULT 0,
    punt_returns INTEGER DEFAULT 0,
    punt_return_yards INTEGER DEFAULT 0,
    punt_return_touchdowns INTEGER DEFAULT 0,
    UNIQUE(game_id, player_id),
    FOREIGN KEY (game_id) REFERENCES game(id) ON DELETE CASCADE,
    FOREIGN KEY (player_id) REFERENCES player(id) ON DELETE CASCADE,
    FOREIGN KEY (team_id) REFERENCES team(id) ON DELETE CASCADE
);
```
