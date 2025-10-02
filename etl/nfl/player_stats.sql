drop MATERIALIZED VIEW if EXISTS nfl.player_stats_season_passing;
drop MATERIALIZED VIEW if EXISTS nfl.player_stats_games_passing;

drop MATERIALIZED VIEW if EXISTS nfl.player_stats_season_rushing;
drop MATERIALIZED VIEW if EXISTS nfl.player_stats_games_rushing;
------------------------------------------------------------------------------------------------------------------------
create MATERIALIZED VIEW nfl.player_stats_games_passing AS
SELECT
    ps."player_id"
  , pif."first_name"
  , pif."last_name"
  , pif."display_name"
  , pif."football_name"
  , pif."position"
  , pif."position_group"
  , ps."season"
  , ps."week"
  , ps."season_type" AS "game_type"
  , ps."team"
  , ps."opponent_team"
  , ps."completions"
  , ps."attempts"
  , ps."passing_yards"
  , ps."passing_tds"
  , ps."passing_interceptions"
  , ps."sacks_suffered"
  , ps.sack_yards_lost
  , ps.sack_fumbles
  , ps."sack_fumbles_lost"
  , ps.passing_air_yards
  , ps."passing_yards_after_catch"
  , ps.passing_first_downs
  , ps."passing_2pt_conversions"
FROM nfl."combined_player_stats" ps
         LEFT JOIN nfl."players_info" pif ON ps."player_id" = pif."gsis_id";

------------------------------------------------------------------------------------------------------------------------
create MATERIALIZED VIEW nfl.player_stats_season_passing AS
SELECT
    ps."player_id"
  , pif."first_name"
  , pif."last_name"
  , pif."position"
  , pif."position_group"
  , pif."display_name"
  , pif."football_name"
  , ps."season"
  , ps."team"
  , SUM(ps."completions") AS completions
  , SUM(ps."attempts") AS attempts
  , SUM(ps."passing_yards") AS passing_yards
  , SUM(ps."passing_tds") AS passing_tds
  , SUM(ps."passing_interceptions") AS passing_interceptions
  , SUM(ps."sacks_suffered") AS sacks_suffered
  , SUM(ps.sack_yards_lost) AS sack_yards_lost
  , SUM(ps.sack_fumbles) AS sack_fumbles
  , SUM(ps."sack_fumbles_lost") AS sack_fumbles_lost
  , SUM(ps.passing_air_yards) AS passing_air_yards
  , SUM(ps."passing_yards_after_catch") AS passing_yards_after_catch
  , SUM(ps.passing_first_downs) AS passing_first_downs
  , SUM(ps."passing_2pt_conversions") AS passing_2pt_conversions
FROM nfl.player_stats_games_passing ps
         LEFT JOIN nfl."players_info" pif ON ps."player_id" = pif."gsis_id"
GROUP BY "ps"."player_id", "pif"."first_name", "pif"."last_name", "pif"."position", "pif"."position_group"
       , "pif"."display_name", "pif"."football_name", "ps"."season", "ps"."team";

------------------------------------------------------------------------------------------------------------------------
create MATERIALIZED VIEW nfl.player_stats_games_rushing AS
SELECT
    ps."player_id"
  , pif."first_name"
  , pif."last_name"
  , pif."display_name"
  , pif."football_name"
  , pif."position"
  , pif."position_group"
  , ps."season"
  , ps."week"
  , ps."season_type" AS "game_type"
  , ps."team"
  , ps."opponent_team"
  , ps."carries"
  , ps."rushing_yards"
  , ps."rushing_tds"
  , ps."rushing_fumbles"
  , ps."rushing_fumbles_lost"
  , ps."rushing_first_downs"
  , ps."rushing_2pt_conversions"
FROM nfl."combined_player_stats" ps
         LEFT JOIN nfl."players_info" pif ON ps."player_id" = pif."gsis_id";

------------------------------------------------------------------------------------------------------------------------
create MATERIALIZED VIEW nfl.player_stats_season_rushing AS
SELECT
    ps."player_id"
  , pif."first_name"
  , pif."last_name"
  , pif."display_name"
  , pif."football_name"
  , pif."position"
  , pif."position_group"
  , ps."season"
  , ps."game_type" AS "game_type"
  , ps."team"
  , SUM(ps."carries") AS carries
  , SUM(ps."rushing_yards") AS rushing_yards
  , SUM(ps."rushing_tds") AS rushing_tds
  , SUM(ps."rushing_fumbles") as rushing_fumbles
  , SUM(ps."rushing_fumbles_lost") AS rushing_fumbles_lost
  , SUM(ps."rushing_first_downs") AS rushing_first_downs
  , SUM(ps."rushing_2pt_conversions") AS rushing_2pt_conversions
FROM nfl."player_stats_games_rushing" ps
         LEFT JOIN nfl."players_info" pif ON ps."player_id" = pif."gsis_id"
GROUP BY "ps"."player_id", "pif"."first_name", "pif"."last_name", "pif"."position", "pif"."position_group"
       , "pif"."display_name", "pif"."football_name", "ps"."season", ps."game_type", "ps"."team";

------------------------------------------------------------------------------------------------------------------------
drop MATERIALIZED VIEW if EXISTS nfl.player_stats_games_receiving;
create MATERIALIZED VIEW nfl.player_stats_games_receiving AS
SELECT
    ps."player_id"
  , pif."first_name"
  , pif."last_name"
  , pif."display_name"
  , pif."football_name"
  , ps."season"
  , ps."week"
  , ps."season_type" AS "game_type"
  , ps."team"
  , ps."opponent_team"
  , ps."receptions"
  , ps."targets"
  , ps.receiving_yards
  , ps.receiving_tds
  , ps.receiving_fumbles
  , ps.receiving_fumbles_lost
  , ps.receiving_air_yards
  , ps.receiving_yards_after_catch
  , ps.receiving_first_downs
  , ps.receiving_2pt_conversions
  , ps.target_share
FROM nfl."combined_player_stats" ps
         LEFT JOIN nfl."players_info" pif ON ps."player_id" = pif."gsis_id"

------------------------------------------------------------------------------------------------------------------------
drop MATERIALIZED VIEW if EXISTS nfl.player_stats_games_defense;
create MATERIALIZED VIEW nfl.player_stats_games_defense AS
SELECT
    ps."player_id"
  , pif."first_name"
  , pif."last_name"
  , pif."display_name"
  , pif."football_name"
  , ps."season"
  , ps."week"
  , ps."season_type" AS "game_type"
  , ps."team"
  , ps."opponent_team"
  , ps."def_tackles_solo"
  , ps."def_tackles_with_assist"
  , ps."def_tackle_assists"
  , ps."def_tackles_for_loss"
  , ps."def_tackles_for_loss_yards"
  , ps."def_fumbles_forced"
  , ps."def_sacks"
  , ps."def_sack_yards"
  , ps."def_qb_hits"
  , ps."def_interceptions"
  , ps."def_interception_yards"
  , ps.def_pass_defended
  , ps.def_tds
  , ps."def_fumbles"
  , ps."def_safeties"
FROM nfl."combined_player_stats" ps
         LEFT JOIN nfl."players_info" pif ON ps."player_id" = pif."gsis_id"

------------------------------------------------------------------------------------------------------------------------
drop materialized view IF EXISTS nfl.player_stats_games_kicking;
create MATERIALIZED VIEW nfl.player_stats_games_kicking AS
SELECT
    ps."player_id"
  , pif."first_name"
  , pif."last_name"
  , pif."display_name"
  , pif."football_name"
  , ps."season"
  , ps."week"
  , ps."season_type" AS "game_type"
  , ps."team"
  , ps."opponent_team"
  , ps."fg_made"
  , ps."fg_att"
  , ps.fg_missed
  , ps."fg_blocked"
  , ps."fg_made_0_19"
  , ps."fg_made_20_29"
  , ps."fg_made_30_39"
  , ps."fg_made_40_49"
  , ps."fg_made_50_59"
  , ps."fg_made_60_"
  , ps."fg_missed_0_19"
  , ps."fg_missed_20_29"
  , ps."fg_missed_30_39"
  , ps."fg_missed_40_49"
  , ps."fg_missed_50_59"
  , ps."fg_missed_60_"
  , ps."pat_made"
  , ps."pat_att"
FROM nfl."combined_player_stats" ps
         LEFT JOIN nfl."players_info" pif ON ps."player_id" = pif."gsis_id"

------------------------------------------------------------------------------------------------------------------------