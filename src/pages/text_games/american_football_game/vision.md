# Vision

This document outlines the core vision for American Football Sim, a game that blends deep football management with personal role-playing.

## The Playable Character

The player experience is centered around the life and career of a professional in the world of American football.

### Core Role: A Career in Football
The player's experience is centered on a character navigating a career within the world of American football. While the primary gameplay focuses on management roles like a General Manager or Team Owner, the game fully supports other playstyles. Players can act as a hands-off 'commissioner' to simply observe the simulation unfold, or even experience periods of unemployment due to personal choice or professional setbacks. Your story is defined by your character's journey, not just their job title.

### Measuring Success: The Legacy Score
In Football Sim, success is measured by more than just championships. The ultimate goal is to build a lasting legacy, which is represented by a comprehensive **Legacy Score**. This score provides a tangible way to measure success and compare the outcomes of different playthroughs, and it's composed of two core components:

*   **Professional Legacy:** This is a measure of your success in the football world. It includes traditional metrics like wins and losses, championships won, financial management, and the growth of your team's prestige.
*   **Personal Legacy:** This measures your success as a person. It is influenced by the strength of your relationships with family and friends, your personal wealth, your health, and whether you successfully create a lineage to carry on your name.

This dual system allows for multiple paths to victory. A player could become a ruthless, championship-winning tyrant with a broken family life, or a beloved figure who builds a dynasty of both football and family, each resulting in a unique Legacy Score.

### Life Simulation and Legacy

The game extends beyond the stadium. Players will manage their character's personal life, which can include dating, marriage, and raising children. The game features a legacy system: upon the character's death, the player may have the option to continue as an heir who follows in their footsteps, creating a multi-generational saga. Conversely, a character who fails to build relationships may find their story ends with them.

### A World of Consequences

Decisions have cascading effects that ripple through the character's life. The game will feature a dynamic economy and requires players to manage their personal finances.

- **Work-Life Balance:** Neglecting personal issues can lead to long-term health problems and stress, eventually impacting job performance. Conversely, focusing too much on personal life can cause professional duties to suffer.
- **Interconnected Choices:** Every decision can create a new dilemma, making each playthrough a unique narrative driven by the player's choices.

## AI-Driven Narrative Engine

A core pillar of the game's design is the use of a Large Language Model (LLM) to generate dynamic, contextual, and unique narrative content. This moves beyond static, pre-written events to create a truly emergent story.

### NPC State System

Every non-player character (NPC) in the world maintains a persistent state, tracking their personality, relationships, history, and current situation. This state serves as the context for the LLM, ensuring that all generated content is deeply personal and relevant to that character's life.

### Emergent Storytelling

Instead of generic events, the game will use the LLM to create specific, flavorful narratives. This approach enhances immersion and makes the game world feel alive.

- **Dynamic Event Descriptions:** If a player suffers a "non-football injury," the game won't just report the fact. It will query the LLM with the player's state (e.g., personality, recent activities) to generate a plausible and unique cause, such as "slipped on a wet floor while trying to recreate a viral dance video for his social media."
- **Contextual Performance Modifiers:** When a player receives a performance boost or penalty, the reason will be dynamically generated. A morale boost might be attributed to "receiving a heartfelt letter from his hometown Little League team," while a slump could be explained by "an ongoing obsession with a difficult video game, leading to sleepless nights."
- **Relationship-Driven Decisions:** The history between NPCs will influence their choices. For instance, a star player entering free agency might be heavily swayed to sign with a team where his former college teammate plays, especially if they have maintained a strong, positive relationship over their careers. This creates subtle, realistic narratives that reward attentive players.

This system ensures that no two playthroughs are the same and that the stories unfolding around the player are as deep and varied as the football simulation itself.

## Leagues

The game world is structured around a tiered system of football leagues.

### League Structure

The game world is organized into a hierarchy of leagues, each with its own rules and characteristics.

- **Professional vs. Amateur:** A league's core identity is defined by how its players are compensated.

	- **Professional Leagues:** Players are paid employees who receive salaries. Their decisions are heavily influenced by financial incentives, career ambitions, and the desire to win championships.

	- **Amateur Leagues:** Players are not paid a salary. Instead, they might receive benefits like scholarships. Their motivations are balanced differently, with real-world constraints like geographic location and time commitment playing a larger role in their decisions than for professionals.

- **Prestige:** Every league and team has a prestige score, representing their reputation and historical success. High prestige is crucial for attracting top-tier players and staff in both professional and amateur tiers, but especially so in the amateurs.
- **Flexible Size:** While a typical league might consist of 8 to 32 teams, the system will be designed to support a flexible number of teams, allowing for diverse game worlds.

### The Annual Cycle

The game calendar revolves around a distinct annual schedule. A typical season cycle proceeds as follows:

```
Offseason 							-->
End-of-Season Awards                -->
Staff Changes  						-->
New Player Generation (Draft)       -->
Free Agency & Contract Negotiations -->
Player Retirements 					-->
Training Camp 						--> 
Preseason 							-->
Season 								-->
Playoffs 							-->
Offseason
```

### The Weekly Cycle (In-Season)

While the annual cycle dictates the world's high-level progression, the in-season period is structured around a weekly, turn-based loop. This provides the cadence for team management, game events, and the personal-life decisions the player may face.

```
Start of Week (Post-Game)         -->
Media Interactions & Fallout      -->
Personal Life Decisions           -->
Roster & Depth Chart Adjustments  -->
Weekly Training & Practice        -->
Mid-Week Events & Decisions       -->
Pre-Game Preparation & Strategy   -->
Next Game
```

### Gameplay Pacing and Player Agency

A key design goal is to allow a single season to be simulated in approximately 20 minutes for players who wish to advance quickly, ensuring a fast and fluid experience for those who prefer it. However, the game is designed to be enjoyed at a slower pace. Player agency is deep, covering team management tasks like designing playbooks, setting ticket prices, and running marketing campaigns.

This agency also extends to the character's personal life. Players will face numerous non-football decisions that shape their story. Just like their professional duties, these choices can be addressed with minimal thought to advance the timeline, or they can be approached tactically, requiring careful consideration of their cascading consequences.
