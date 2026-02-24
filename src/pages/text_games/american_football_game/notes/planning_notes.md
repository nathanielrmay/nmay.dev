Role-Playing & Narrative Integration:Character System:

What specific events will the LLM generate?
How will you prompt the LLM to generate relevant and consistent narratives?
How will LLM-generated content tie into game mechanics?

Multi-League & Tiered System:
Determine leauge parameters
Design the rules for each league type (pro, college, international, arena).
How will promotions/relegations work?
How will player movement between leagues be handled?


Season Schedule - Schedule that a league will follow. This doesn't contain inividual game dates but the foramt a league will follow.

Simulation Configuration - (tick rate, randomness factors, etc.) I suppose this would go in the world table.

User instuction log -
• Idea: The MVP lets the user give limited instructions that ultimately let the simulation engine decide the play outcome. Keeping a history of these instructions might be useful for reporting or debugging.
• Change: Add a small table (e.g., coach_instruction_log) that records the team_id, game_id, quarter, play_number, and the instruction/decision made. This is optional for MVP but may be valuable to demonstrate player–coach interaction.

Coach play calling proclivity
play_calling_skill
risk_tolerance
preferred_style

Play calling notes during the game that an llm coach can reference.

Play-by-Play Commentary Templates
