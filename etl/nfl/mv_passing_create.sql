drop MATERIALIZED VIEW IF EXISTS nfl.player_passing_log_drives;
drop MATERIALIZED VIEW IF EXISTS nfl.player_passing_log_games;
drop MATERIALIZED VIEW IF EXISTS nfl."player_passing_log_seasons";

drop MATERIALIZED VIEW IF EXISTS nfl."player_passing_pbp_combined";

drop MATERIALIZED VIEW IF EXISTS nfl."player_passing_pbp_interceptions";
drop MATERIALIZED VIEW IF EXISTS nfl."player_passing_pbp_sacks";
drop MATERIALIZED VIEW IF EXISTS nfl."player_passing_pbp_pat2";
drop MATERIALIZED VIEW IF EXISTS nfl."player_passing_pbp_other";
drop MATERIALIZED VIEW IF EXISTS nfl."player_passing_pbp_fumbles";
drop MATERIALIZED VIEW IF EXISTS nfl."player_passing_pbp";

drop MATERIALIZED VIEW IF EXISTS nfl."player_rushing_log_drive";
drop MATERIALIZED VIEW IF EXISTS nfl."player_rushing_log_weekly";
drop MATERIALIZED VIEW IF EXISTS nfl."player_rushing_log_games";
drop materialized view if exists nfl.player_rushing_log_season;

drop materialized view if exists nfl.player_rushing_combined_pbp;

drop materialized view if exists nfl.player_rushing_pbp;

------------------------------------------------------------------------------------------------------------------------------
create materialized view nfl.player_passing_pbp as
SELECT
    pbp.play_id
  , pbp."passer_player_id"
  , p_info.first_name
  , p_info.last_name
  , pbp.game_id
  , pbp.game_date::DATE                     AS game_date
  , EXTRACT(DAY FROM pbp.game_date::DATE)   AS game_date_day
  , EXTRACT(MONTH FROM pbp.game_date::DATE) AS game_date_month
  , EXTRACT(YEAR FROM pbp.game_date::DATE)  AS game_date_year
  , sch.weekday
  , sch.home_team
  , sch.home_score                          AS final_home_score
  , sch.away_team
  , sch.away_score                          AS final_away_score
  , sch.total
  , sch.location
  , sch.home_coach
  , sch.away_coach
  , sch.stadium
  , sch.roof
  , sch.surface
  , sch.temp
  , sch.wind
  , pbp.season
  , pbp.week
  , sch.game_type
  , pbp.drive
  , pbp.total_home_score
  , pbp.total_away_score
  , pbp.play_type
  , pbp.play_type_nfl
  , pbp.down
  , pbp.ydstogo
  , pbp."pass_attempt"
  , pbp.complete_pass
  , pbp.sack
  , pbp.passing_yards
  , pbp.pass_touchdown
  , pbp.interception
  , pbp.fumble
  , pbp.penalty
  , pbp.penalty_type
  , pbp.air_yards
  , pbp.yards_after_catch
  , pbp.yards_gained
  , pbp.pass_length
  , pbp.pass_location
  , pbp.qb_hit
  , pbp.two_point_attempt
  , pbp.two_point_conv_result
FROM    "nfl"."combined_play_by_play" pbp
            LEFT JOIN nfl.players_ids p_ids ON pbp.passer_player_id = p_ids.gsis_id
            LEFT JOIN nfl.players_info p_info ON pbp.passer_player_id = p_info.gsis_id
            LEFT JOIN nfl.combined_schedules sch ON pbp.game_id = sch.game_id
WHERE ( pbp.play_type='pass'::TEXT AND "play_type_nfl"='PASS'::TEXT ) OR (pbp."play_type"='qb_spike' AND "play_type_nfl"='PASS');
alter materialized view nfl.player_passing_pbp owner to than;

--------------------------------------------------------------------------------------------------------------------------------
create materialized view nfl.player_passing_pbp_interceptions as
SELECT
    pbp.play_id
  , pbp.passer_player_id
  , p_info.first_name
  , p_info.last_name
  , pbp.game_id
  , pbp.game_date::DATE                     AS game_date
  , EXTRACT(DAY FROM pbp.game_date::DATE)   AS game_date_day
  , EXTRACT(MONTH FROM pbp.game_date::DATE) AS game_date_month
  , EXTRACT(YEAR FROM pbp.game_date::DATE)  AS game_date_year
  , sch.weekday
  , sch.home_team
  , sch.home_score                          AS final_home_score
  , sch.away_team
  , sch.away_score                          AS final_away_score
  , sch.total
  , sch.location
  , sch.home_coach
  , sch.away_coach
  , sch.stadium
  , sch.roof
  , sch.surface
  , sch.temp
  , sch.wind
  , pbp.season
  , pbp.week
  , sch.game_type
  , pbp.drive
  , pbp.total_home_score
  , pbp.total_away_score
  , pbp.play_type
  , pbp.play_type_nfl
  , pbp.down
  , pbp.ydstogo
  , pbp.pass_attempt  AS pass_attempt
  , pbp.complete_pass
  , pbp.sack
  , pbp.passing_yards
  , pbp.pass_touchdown
  , pbp.interception
  , pbp.fumble
  , pbp.penalty
  , pbp.penalty_type
  , pbp.air_yards
  , pbp.yards_after_catch
  , pbp.yards_gained
  , pbp.pass_length
  , pbp.pass_location
  , pbp.qb_hit
  , pbp.two_point_attempt
  , pbp.two_point_conv_result
FROM
    nfl.combined_play_by_play pbp
        LEFT JOIN nfl.players_ids p_ids ON pbp.passer_player_id = p_ids.gsis_id
        LEFT JOIN nfl.players_info p_info ON pbp.passer_player_id = p_info.gsis_id
        LEFT JOIN nfl.combined_schedules sch ON pbp.game_id = sch.game_id
WHERE pbp.play_type = 'pass'::TEXT AND pbp."play_type_nfl" = 'INTERCEPTION'::TEXT;
alter materialized view nfl.player_passing_pbp_interceptions owner to than;

--------------------------------------------------------------------------------------------------------------------------------
create materialized view nfl.player_passing_pbp_sacks as
SELECT
    pbp.play_id
  , pbp.passer_player_id
  , p_info.first_name
  , p_info.last_name
  , pbp.game_id
  , pbp.game_date::DATE                     AS game_date
  , EXTRACT(DAY FROM pbp.game_date::DATE)   AS game_date_day
  , EXTRACT(MONTH FROM pbp.game_date::DATE) AS game_date_month
  , EXTRACT(YEAR FROM pbp.game_date::DATE)  AS game_date_year
  , sch.weekday
  , sch.home_team
  , sch.home_score                          AS final_home_score
  , sch.away_team
  , sch.away_score                          AS final_away_score
  , sch.total
  , sch.location
  , sch.home_coach
  , sch.away_coach
  , sch.stadium
  , sch.roof
  , sch.surface
  , sch.temp
  , sch.wind
  , pbp.season
  , pbp.week
  , sch.game_type
  , pbp.drive
  , pbp.total_home_score
  , pbp.total_away_score
  , pbp.play_type
  , pbp.play_type_nfl
  , pbp.down
  , pbp.ydstogo
  , 0 as pass_attempt
  , pbp.complete_pass
  , pbp.sack
  , pbp.passing_yards
  , pbp.pass_touchdown
  , pbp.interception
  , pbp.fumble
  , pbp.penalty
  , pbp.penalty_type
  , pbp.air_yards
  , pbp.yards_after_catch
  , pbp.yards_gained
  , pbp.pass_length
  , pbp.pass_location
  , pbp.qb_hit
  , pbp.two_point_attempt
  , pbp.two_point_conv_result
FROM
    nfl.combined_play_by_play pbp
        LEFT JOIN nfl.players_ids p_ids ON pbp.passer_player_id = p_ids.gsis_id
        LEFT JOIN nfl.players_info p_info ON pbp.passer_player_id = p_info.gsis_id
        LEFT JOIN nfl.combined_schedules sch ON pbp.game_id = sch.game_id
WHERE pbp.play_type = 'pass'::TEXT AND pbp."play_type_nfl" = 'SACK'::TEXT;
alter materialized view nfl.player_passing_pbp_sacks owner to than;

--------------------------------------------------------------------------------------------------------------------------------
create materialized view nfl.player_passing_pbp_pat2 as
SELECT
    pbp.play_id
  , pbp.passer_player_id
  , p_info.first_name
  , p_info.last_name
  , pbp.game_id
  , pbp.game_date::DATE                     AS game_date
  , EXTRACT(DAY FROM pbp.game_date::DATE)   AS game_date_day
  , EXTRACT(MONTH FROM pbp.game_date::DATE) AS game_date_month
  , EXTRACT(YEAR FROM pbp.game_date::DATE)  AS game_date_year
  , sch.weekday
  , sch.home_team
  , sch.home_score                          AS final_home_score
  , sch.away_team
  , sch.away_score                          AS final_away_score
  , sch.total
  , sch.location
  , sch.home_coach
  , sch.away_coach
  , sch.stadium
  , sch.roof
  , sch.surface
  , sch.temp
  , sch.wind
  , pbp.season
  , pbp.week
  , sch.game_type
  , pbp.drive
  , pbp.total_home_score
  , pbp.total_away_score
  , pbp.play_type
  , pbp.play_type_nfl
  , pbp.down
  , pbp.ydstogo
  , pbp.pass_attempt
  , pbp.complete_pass
  , pbp.sack
  , pbp.passing_yards
  , pbp.pass_touchdown
  , pbp.interception
  , pbp.fumble
  , pbp.penalty
  , pbp.penalty_type
  , pbp.air_yards
  , pbp.yards_after_catch
  , pbp.yards_gained
  , pbp.pass_length
  , pbp.pass_location
  , pbp.qb_hit
  , pbp.two_point_attempt
  , pbp.two_point_conv_result
FROM
    nfl.combined_play_by_play pbp
        LEFT JOIN nfl.players_ids p_ids ON pbp.passer_player_id = p_ids.gsis_id
        LEFT JOIN nfl.players_info p_info ON pbp.passer_player_id = p_info.gsis_id
        LEFT JOIN nfl.combined_schedules sch ON pbp.game_id = sch.game_id
WHERE pbp.play_type = 'pass'::TEXT AND pbp."play_type_nfl" = 'PAT2'::TEXT;
alter materialized view nfl.player_passing_pbp_pat2 owner to than;

--------------------------------------------------------------------------------------------------------------------------------
create materialized view nfl.player_passing_pbp_other as
SELECT
    pbp.play_id
  , pbp.passer_player_id
  , p_info.first_name
  , p_info.last_name
  , pbp.game_id
  , pbp.game_date::DATE                     AS game_date
  , EXTRACT(DAY FROM pbp.game_date::DATE)   AS game_date_day
  , EXTRACT(MONTH FROM pbp.game_date::DATE) AS game_date_month
  , EXTRACT(YEAR FROM pbp.game_date::DATE)  AS game_date_year
  , sch.weekday
  , sch.home_team
  , sch.home_score                          AS final_home_score
  , sch.away_team
  , sch.away_score                          AS final_away_score
  , sch.total
  , sch.location
  , sch.home_coach
  , sch.away_coach
  , sch.stadium
  , sch.roof
  , sch.surface
  , sch.temp
  , sch.wind
  , pbp.season
  , pbp.week
  , sch.game_type
  , pbp.drive
  , pbp.total_home_score
  , pbp.total_away_score
  , pbp.play_type
  , pbp.play_type_nfl
  , pbp.down
  , pbp.ydstogo
  , pbp.pass_attempt
  , pbp.complete_pass
  , pbp.sack
  , pbp.passing_yards
  , pbp.pass_touchdown
  , pbp.interception
  , pbp.fumble
  , pbp.penalty
  , pbp.penalty_type
  , pbp.air_yards
  , pbp.yards_after_catch
  , pbp.yards_gained
  , pbp.pass_length
  , pbp.pass_location
  , pbp.qb_hit
  , pbp.two_point_attempt
  , pbp.two_point_conv_result
FROM
    nfl.combined_play_by_play pbp
        LEFT JOIN nfl.players_ids p_ids ON pbp.passer_player_id = p_ids.gsis_id
        LEFT JOIN nfl.players_info p_info ON pbp.passer_player_id = p_info.gsis_id
        LEFT JOIN nfl.combined_schedules sch ON pbp.game_id = sch.game_id
WHERE pbp.play_type = 'pass'::TEXT AND pbp."play_type_nfl" = 'UNSPECIFIED'::TEXT;
alter materialized view nfl.player_passing_pbp_other owner to than;

--------------------------------------------------------------------------------------------------------------------------------
create materialized view nfl.player_passing_pbp_fumbles as
SELECT
    pbp.play_id
  , pbp.passer_player_id
  , p_info.first_name
  , p_info.last_name
  , pbp.game_id
  , pbp.game_date::DATE                     AS game_date
  , EXTRACT(DAY FROM pbp.game_date::DATE)   AS game_date_day
  , EXTRACT(MONTH FROM pbp.game_date::DATE) AS game_date_month
  , EXTRACT(YEAR FROM pbp.game_date::DATE)  AS game_date_year
  , sch.weekday
  , sch.home_team
  , sch.home_score                          AS final_home_score
  , sch.away_team
  , sch.away_score                          AS final_away_score
  , sch.total
  , sch.location
  , sch.home_coach
  , sch.away_coach
  , sch.stadium
  , sch.roof
  , sch.surface
  , sch.temp
  , sch.wind
  , pbp.season
  , pbp.week
  , sch.game_type
  , pbp.drive
  , pbp.total_home_score
  , pbp.total_away_score
  , pbp.play_type
  , pbp.play_type_nfl
  , pbp.down
  , pbp.ydstogo
  , pbp.pass_attempt
  , pbp.complete_pass
  , pbp.sack
  , pbp.passing_yards
  , pbp.pass_touchdown
  , pbp.interception
  , pbp.fumble
  , pbp.penalty
  , pbp.penalty_type
  , pbp.air_yards
  , pbp.yards_after_catch
  , pbp.yards_gained
  , pbp.pass_length
  , pbp.pass_location
  , pbp.qb_hit
  , pbp.two_point_attempt
  , pbp.two_point_conv_result
FROM
    nfl.combined_play_by_play pbp
        LEFT JOIN nfl.players_ids p_ids ON pbp.passer_player_id = p_ids.gsis_id
        LEFT JOIN nfl.players_info p_info ON pbp.passer_player_id = p_info.gsis_id
        LEFT JOIN nfl.combined_schedules sch ON pbp.game_id = sch.game_id
WHERE pbp.play_type = 'pass'::TEXT
  AND pbp.play_type_nfl = 'FUMBLE_RECOVERED_BY_OPPONENT'::TEXT;
alter materialized view nfl.player_passing_pbp_fumbles owner to than;

--------------------------------------------------------------------------------------------------------------------------------
create materialized view nfl.player_passing_pbp_combined as
SELECT
    pbp.play_id
  , pbp."passer_player_id"
  , p_info.first_name
  , p_info.last_name
  , pbp.game_id
  , pbp.game_date::DATE                     AS game_date
  , EXTRACT(DAY FROM pbp.game_date::DATE)   AS game_date_day
  , EXTRACT(MONTH FROM pbp.game_date::DATE) AS game_date_month
  , EXTRACT(YEAR FROM pbp.game_date::DATE)  AS game_date_year
  , sch.weekday
  , sch.home_team
  , sch.home_score                          AS final_home_score
  , sch.away_team
  , sch.away_score                          AS final_away_score
  , sch.total
  , sch.location
  , sch.home_coach
  , sch.away_coach
  , sch.stadium
  , sch.roof
  , sch.surface
  , sch.temp
  , sch.wind
  , pbp.season
  , pbp.week
  , sch.game_type
  , pbp.drive
  , pbp.total_home_score
  , pbp.total_away_score
  , pbp.play_type
  , pbp.play_type_nfl
  , pbp.down
  , pbp.ydstogo
  , pbp."pass_attempt"
  , pbp.complete_pass
  , pbp.sack
  , pbp.passing_yards
  , pbp.pass_touchdown
  , pbp.interception
  , pbp.fumble
  , pbp.penalty
  , pbp.penalty_type
  , pbp.air_yards
  , pbp.yards_after_catch
  , pbp.yards_gained
  , pbp.pass_length
  , pbp.pass_location
  , pbp.qb_hit
  , pbp.two_point_attempt
  , pbp.two_point_conv_result
FROM        (
                SELECT * FROM "nfl".player_passing_pbp
                UNION ALL
                SELECT * FROM "nfl"."player_passing_pbp_sacks"
                UNION ALL
                SELECT * FROM "nfl"."player_passing_pbp_interceptions"
                UNION ALL
                SELECT * FROM "nfl"."player_passing_pbp_fumbles"
            ) pbp
                LEFT JOIN nfl.players_ids p_ids ON pbp.passer_player_id = p_ids.gsis_id
                LEFT JOIN nfl.players_info p_info ON pbp.passer_player_id = p_info.gsis_id
                LEFT JOIN nfl.combined_schedules sch ON pbp.game_id = sch.game_id;
alter materialized view nfl.player_passing_pbp owner to than;

--------------------------------------------------------------------------------------------------------------------------------
create materialized view nfl.player_passing_log_drives as
SELECT
    pbp.passer_player_id
  , p_info.first_name
  , p_info.last_name
  , pbp.game_id
  , pbp.game_date
  , pbp.game_date_day
  , pbp.game_date_month
  , pbp.game_date_year
  , pbp.home_team
  , pbp.final_home_score
  , pbp.away_team
  , pbp.final_away_score
  , sch.total
  , sch.location
  , pbp.home_coach
  , pbp.away_coach
  , pbp.stadium
  , pbp.roof
  , pbp.surface
  , pbp.temp
  , pbp.wind
  , pbp.season
  , pbp.week
  , pbp.game_type
  , pbp.drive
  , pbp.total_home_score
  , pbp.total_away_score
  , SUM(pbp.complete_pass)                                                AS complete_passes
  , SUM(pbp.pass_attempt)                                                 AS pass_attempts
  , SUM(pbp.passing_yards)                                                AS passing_yards
  , SUM(pbp.pass_touchdown)                                               AS touchdowns
  , SUM(pbp.interception)                                                 AS interceptions
  , SUM(pbp.fumble)                                                       AS fumbles
  , SUM(pbp.air_yards)                                                    AS air_yards
  , SUM(pbp.yards_after_catch)                                            AS yards_after_catch
  , SUM(pbp.yards_gained)                                                 AS yards_gained
  , NULLIF(COUNT(*) FILTER (WHERE pbp.pass_length = 'short'::TEXT), 0)    AS passes_short
  , NULLIF(COUNT(*) FILTER (WHERE pbp.pass_length = 'deep'::TEXT), 0)     AS passes_deep
  , NULLIF(COUNT(*) FILTER (WHERE pbp.pass_location = 'left'::TEXT), 0)   AS passes_left
  , NULLIF(COUNT(*) FILTER (WHERE pbp.pass_location = 'middle'::TEXT), 0) AS passes_middle
  , NULLIF(COUNT(*) FILTER (WHERE pbp.pass_location = 'right'::TEXT), 0)  AS passes_right
  , SUM(pbp.qb_hit)                                                       AS qb_hits
FROM
    "nfl"."player_passing_pbp_combined" pbp
        LEFT JOIN nfl.players_ids p_ids ON pbp.passer_player_id = p_ids.gsis_id
        LEFT JOIN nfl.players_info p_info ON pbp.passer_player_id = p_info.gsis_id
        LEFT JOIN nfl.combined_schedules sch ON pbp.game_id = sch.game_id
GROUP BY pbp.passer_player_id, p_info.first_name, p_info.last_name, pbp.game_id, pbp.game_date, pbp.season, pbp.week
       , pbp.home_team, pbp.away_team, pbp.drive, pbp.total_home_score, pbp.total_away_score, pbp.home_coach
       , pbp.away_coach, pbp.stadium, pbp.roof, pbp.game_date_day, pbp.game_date_month, pbp.game_date_year
       , pbp.final_home_score, pbp.final_away_score, sch.total, sch.location, pbp.surface, pbp.temp, pbp.wind
       , pbp.game_type;
alter materialized view nfl.player_passing_log_drives owner to than;

--------------------------------------------------------------------------------------------------------------------------------
create materialized view nfl.player_passing_log_games as
SELECT
    pbp.passer_player_id
  , p_info.first_name
  , p_info.last_name
  , pbp.game_id
  , pbp.game_date
  , pbp.game_date_day
  , pbp.game_date_month
  , pbp.game_date_year
  , pbp.home_team
  , pbp.away_team
  , pbp.home_coach
  , pbp.away_coach
  , pbp.stadium
  , pbp.roof
  , pbp.surface
  , pbp.temp
  , pbp.wind
  , pbp.season
  , pbp.week
  , pbp.game_type
  , SUM(pbp.complete_pass)                                                AS complete_passes
  , SUM(pbp.pass_attempt)                                                 AS pass_attempts
  , SUM(pbp.passing_yards)                                                AS passing_yards
  , SUM(pbp.pass_touchdown)                                               AS touchdowns
  , SUM(pbp.interception)                                                 AS interceptions
  , SUM(pbp.fumble)                                                       AS fumbles
  , SUM(pbp.air_yards)                                                    AS air_yards
  , SUM(pbp.yards_after_catch)                                            AS yards_after_catch
  , SUM(pbp.yards_gained)                                                 AS yards_gained
  , NULLIF(COUNT(*) FILTER (WHERE pbp.pass_length = 'short'::TEXT), 0)    AS passes_short
  , NULLIF(COUNT(*) FILTER (WHERE pbp.pass_length = 'deep'::TEXT), 0)     AS passes_deep
  , NULLIF(COUNT(*) FILTER (WHERE pbp.pass_location = 'left'::TEXT), 0)   AS passes_left
  , NULLIF(COUNT(*) FILTER (WHERE pbp.pass_location = 'middle'::TEXT), 0) AS passes_middle
  , NULLIF(COUNT(*) FILTER (WHERE pbp.pass_location = 'right'::TEXT), 0)  AS passes_right
  , SUM(pbp.qb_hit)                                                       AS qb_hits
FROM
    "nfl"."player_passing_pbp_combined" pbp
        LEFT JOIN nfl.players_ids p_ids ON pbp.passer_player_id = p_ids.gsis_id
        LEFT JOIN nfl.players_info p_info ON pbp.passer_player_id = p_info.gsis_id
        LEFT JOIN nfl.combined_schedules sch ON pbp.game_id = sch.game_id
GROUP BY "pbp"."passer_player_id", "p_info"."first_name", "p_info"."last_name", "pbp"."game_id", "pbp"."game_date"
       , "pbp"."season", "pbp"."week"
       , "pbp"."home_team", "pbp"."away_team", "pbp"."home_coach", "pbp"."away_coach", "pbp"."stadium", "pbp"."roof"
       , "pbp"."game_date_day"
       , "pbp"."game_date_month", "pbp"."game_date_year", "pbp"."surface", "pbp"."temp", "pbp"."wind"
       , "pbp"."game_type";
alter materialized view nfl.player_passing_log_games owner to than;

--------------------------------------------------------------------------------------------------------------------------------
create materialized view nfl.player_passing_log_seasons as
SELECT
    pbp.passer_player_id
  , p_info.first_name
  , p_info.last_name
  , pbp.season
  , pbp.game_type
  , COUNT(DISTINCT pbp.game_id)                                           AS games
  , SUM(pbp.complete_pass)                                                AS complete_passes
  , SUM(pbp.pass_attempt)                                                 AS pass_attempts
  , SUM(pbp.passing_yards)                                                AS passing_yards
  , SUM(pbp.pass_touchdown)                                               AS touchdowns
  , SUM(pbp.interception)                                                 AS interceptions
  , SUM(pbp.fumble)                                                       AS fumbles
  , SUM(pbp.air_yards)                                                    AS air_yards
  , SUM(pbp.yards_after_catch)                                            AS yards_after_catch
  , SUM(pbp.yards_gained)                                                 AS yards_gained
  , NULLIF(COUNT(*) FILTER (WHERE pbp.pass_length = 'short'::TEXT), 0)    AS passes_short
  , NULLIF(COUNT(*) FILTER (WHERE pbp.pass_length = 'deep'::TEXT), 0)     AS passes_deep
  , NULLIF(COUNT(*) FILTER (WHERE pbp.pass_location = 'left'::TEXT), 0)   AS passes_left
  , NULLIF(COUNT(*) FILTER (WHERE pbp.pass_location = 'middle'::TEXT), 0) AS passes_middle
  , NULLIF(COUNT(*) FILTER (WHERE pbp.pass_location = 'right'::TEXT), 0)  AS passes_right
  , SUM(pbp.qb_hit)                                                       AS qb_hits
FROM
    nfl."player_passing_pbp_combined" pbp
        LEFT JOIN nfl.players_ids p_ids ON pbp.passer_player_id = p_ids.gsis_id
        LEFT JOIN nfl.players_info p_info ON pbp.passer_player_id = p_info.gsis_id
        LEFT JOIN nfl.combined_schedules sch ON pbp.game_id = sch.game_id
GROUP BY pbp.passer_player_id, p_info.first_name, p_info.last_name, pbp.season, pbp.game_type;
alter materialized view nfl.player_passing_log_seasons owner to than;

--------------------------------------------------------------------------------------------------------------------------------
create materialized view nfl.player_rushing_pbp as
SELECT
    pbp.play_id
  , pbp."rusher_player_id"
  , p_info.first_name
  , p_info.last_name
  , pbp.game_id
  , pbp.game_date::DATE                     AS game_date
  , EXTRACT(DAY FROM pbp.game_date::DATE)   AS game_date_day
  , EXTRACT(MONTH FROM pbp.game_date::DATE) AS game_date_month
  , EXTRACT(YEAR FROM pbp.game_date::DATE)  AS game_date_year
  , sch.weekday
  , sch.home_team
  , sch.home_score                          AS final_home_score
  , sch.away_team
  , sch.away_score                          AS final_away_score
  , sch.total
  , sch.location
  , sch.home_coach
  , sch.away_coach
  , sch.stadium
  , sch.roof
  , sch.surface
  , sch.temp
  , sch.wind
  , pbp.season
  , pbp.week
  , sch.game_type
  , pbp.drive
  , pbp.total_home_score
  , pbp.total_away_score
  , pbp.play_type
  , pbp.play_type_nfl
  , pbp.down
  , pbp.ydstogo
  , pbp."run_location"
  , pbp."run_gap"
  , pbp."third_down_converted"
  , pbp."third_down_failed"
  , pbp."fumble_lost"
  , pbp."rush_attempt"
  , pbp."rushing_yards"
FROM "nfl"."combined_play_by_play" pbp
         LEFT JOIN nfl.players_ids p_ids ON pbp."rusher_player_id" = p_ids.gsis_id
         LEFT JOIN nfl.players_info p_info ON pbp."rusher_player_id" = p_info.gsis_id
         LEFT JOIN nfl.combined_schedules sch ON pbp.game_id = sch.game_id
WHERE "play_type" = 'run' AND "play_type_nfl"='RUSH';
alter materialized view nfl.player_rushing_pbp owner to than;

--------------------------------------------------------------------------------------------------------------------------------
create materialized view nfl.player_rushing_combined_pbp as
SELECT
    pbp.play_id
  , pbp."rusher_player_id"
  , p_info.first_name
  , p_info.last_name
  , pbp.game_id
  , pbp.game_date::DATE                     AS game_date
  , EXTRACT(DAY FROM pbp.game_date::DATE)   AS game_date_day
  , EXTRACT(MONTH FROM pbp.game_date::DATE) AS game_date_month
  , EXTRACT(YEAR FROM pbp.game_date::DATE)  AS game_date_year
  , sch.weekday
  , sch.home_team
  , sch.home_score                          AS final_home_score
  , sch.away_team
  , sch.away_score                          AS final_away_score
  , sch.total
  , sch.location
  , sch.home_coach
  , sch.away_coach
  , sch.stadium
  , sch.roof
  , sch.surface
  , sch.temp
  , sch.wind
  , pbp.season
  , pbp.week
  , sch.game_type
  , pbp.drive
  , pbp.total_home_score
  , pbp.total_away_score
  , pbp.play_type
  , pbp.play_type_nfl
  , pbp.down
  , pbp.ydstogo
  , pbp."run_location"
  , pbp."run_gap"
  , pbp."third_down_converted"
  , pbp."third_down_failed"
  , pbp."fumble_lost"
  , pbp."rush_attempt"
  , pbp."rushing_yards"
FROM nfl.player_rushing_pbp pbp
         LEFT JOIN nfl.players_ids p_ids ON pbp."rusher_player_id" = p_ids.gsis_id
         LEFT JOIN nfl.players_info p_info ON pbp."rusher_player_id" = p_info.gsis_id
         LEFT JOIN nfl.combined_schedules sch ON pbp.game_id = sch.game_id;
alter materialized view nfl.player_rushing_combined_pbp owner to than;

--------------------------------------------------------------------------------------------------------------------------------
create materialized view nfl.player_rushing_log_drive AS
Select
    --     pbp.play_id
    pbp."rusher_player_id"
  , p_info.first_name
  , p_info.last_name
  , pbp.game_id
  , pbp.game_date::DATE                     AS game_date
  , EXTRACT(DAY FROM pbp.game_date::DATE)   AS game_date_day
  , EXTRACT(MONTH FROM pbp.game_date::DATE) AS game_date_month
  , EXTRACT(YEAR FROM pbp.game_date::DATE)  AS game_date_year
  , sch.weekday
  , sch.home_team
  , sch.home_score                          AS final_home_score
  , sch.away_team
  , sch.away_score                          AS final_away_score
  , sch.total
  , sch.location
  , sch.home_coach
  , sch.away_coach
  , sch.stadium
  , sch.roof
  , sch.surface
  , sch.temp
  , sch.wind
  , pbp.season
  , pbp.week
  , sch.game_type
  , pbp.drive
  , pbp.total_home_score
  , pbp.total_away_score
--   , pbp.play_type
--   , pbp.play_type_nfl
--   , pbp.down
--   , pbp.ydstogo
--   , pbp."run_location"
--   , pbp."run_gap"
  , SUM(pbp."third_down_converted") AS "third_down_converted"
  , SUM(pbp."third_down_failed")   AS "third_down_failed"
  , SUM(pbp."fumble_lost")         AS "fumble_lost"
  , SUM(pbp."rush_attempt")        AS "rush_attempt"
  , SUM(pbp."rushing_yards")       AS "rushing_yards"
FROM
    nfl."player_rushing_combined_pbp" pbp
        LEFT JOIN nfl.players_ids p_ids ON pbp."rusher_player_id" = p_ids.gsis_id
        LEFT JOIN nfl.players_info p_info ON pbp."rusher_player_id" = p_info.gsis_id
        LEFT JOIN nfl.combined_schedules sch ON pbp.game_id = sch.game_id
GROUP BY "pbp"."rusher_player_id", "p_info"."first_name", "p_info"."last_name", "pbp"."game_id", "pbp"."game_date"::DATE
       , EXTRACT(DAY FROM "pbp"."game_date"::DATE), EXTRACT(MONTH FROM "pbp"."game_date"::DATE)
       , EXTRACT(YEAR FROM "pbp"."game_date"::DATE), "sch"."weekday", "sch"."home_team", "sch"."home_score"
       , "sch"."away_team", "sch"."away_score", "sch"."total", "sch"."location", "sch"."home_coach", "sch"."away_coach"
       , "sch"."stadium", "sch"."roof", "sch"."surface", "sch"."temp", "sch"."wind", "pbp"."season", "pbp"."week"
       , "sch"."game_type", "pbp"."drive", "pbp"."total_home_score", "pbp"."total_away_score";
Alter materialized view nfl.player_rushing_log_drive owner to than;

--------------------------------------------------------------------------------------------------------------------------------
create materialized view nfl.player_rushing_log_games AS
Select
--     pbp.play_id
    pbp."rusher_player_id"
  , p_info.first_name
  , p_info.last_name
  , pbp.game_id
  , pbp.game_date::DATE                     AS game_date
  , EXTRACT(DAY FROM pbp.game_date::DATE)   AS game_date_day
  , EXTRACT(MONTH FROM pbp.game_date::DATE) AS game_date_month
  , EXTRACT(YEAR FROM pbp.game_date::DATE)  AS game_date_year
  , sch.weekday
  , sch.home_team
  , sch.home_score                          AS final_home_score
  , sch.away_team
  , sch.away_score                          AS final_away_score
  , sch.total
  , sch.location
  , sch.home_coach
  , sch.away_coach
  , sch.stadium
  , sch.roof
  , sch.surface
  , sch.temp
  , sch.wind
  , pbp.season
  , pbp.week
  , sch.game_type
--   , pbp.drive
--   , pbp.total_home_score
--   , pbp.total_away_score
--   , pbp.play_type
--   , pbp.play_type_nfl
--   , pbp.down
--   , pbp.ydstogo
--   , pbp."run_location"
--   , pbp."run_gap"
  , SUM(pbp."third_down_converted") AS "third_down_converted"
  , SUM(pbp."third_down_failed")   AS "third_down_failed"
  , SUM(pbp."fumble_lost")         AS "fumble_lost"
  , SUM(pbp."rush_attempt")        AS "rush_attempt"
  , SUM(pbp."rushing_yards")       AS "rushing_yards"
FROM
    nfl."player_rushing_combined_pbp" pbp
        LEFT JOIN nfl.players_ids p_ids ON pbp."rusher_player_id" = p_ids.gsis_id
        LEFT JOIN nfl.players_info p_info ON pbp."rusher_player_id" = p_info.gsis_id
        LEFT JOIN nfl.combined_schedules sch ON pbp.game_id = sch.game_id
GROUP BY pbp."rusher_player_id", "p_info"."first_name", "p_info"."last_name", "pbp"."game_date", "pbp"."season", "pbp"."week"
       , "sch"."weekday", "sch"."home_team", "sch"."away_team", "sch"."game_type", "sch"."home_score", "sch"."away_score"
       , "sch"."total", "sch"."location", "sch"."home_coach", "sch"."away_coach", "sch"."stadium", "sch"."roof", "sch"."surface"
       , "sch"."temp", "sch"."wind", pbp."game_id";
ALTER materialized view nfl.player_rushing_log_games owner to than;

--------------------------------------------------------------------------------------------------------------------------------
create materialized view nfl.player_rushing_log_season AS
Select
--     pbp.play_id
    pbp."rusher_player_id"
  , p_info.first_name
  , p_info.last_name
  , count(DISTINCT pbp.game_id)
--   , pbp.game_date::DATE                     AS game_date
--   , EXTRACT(DAY FROM pbp.game_date::DATE)   AS game_date_day
--   , EXTRACT(MONTH FROM pbp.game_date::DATE) AS game_date_month
--   , EXTRACT(YEAR FROM pbp.game_date::DATE)  AS game_date_year
--   , sch.weekday
--   , sch.home_team
--   , sch.home_score                          AS final_home_score
--   , sch.away_team
--   , sch.away_score                          AS final_away_score
--   , sch.total
--   , sch.location
--   , sch.home_coach
--   , sch.away_coach
--   , sch.stadium
--   , sch.roof
--   , sch.surface
--   , sch.temp
--   , sch.wind
  , pbp.season
--   , pbp.week
  , sch.game_type
--   , pbp.drive
--   , pbp.total_home_score
--   , pbp.total_away_score
--   , pbp.play_type
--   , pbp.play_type_nfl
--   , pbp.down
--   , pbp.ydstogo
--   , pbp."run_location"
--   , pbp."run_gap"
  , SUM ( pbp."rush_attempt" ) AS rush_attempts
  , SUM ( pbp."rushing_yards" ) AS rushing_yards
  , SUM ( pbp."third_down_converted" ) AS third_downs_converted
  , SUM ( pbp."third_down_failed" ) AS third_downs_failed
  , SUM ( pbp."fumble_lost" ) AS fumbles_lost
FROM
    nfl."player_rushing_combined_pbp" pbp
        LEFT JOIN nfl.players_ids p_ids ON pbp."rusher_player_id" = p_ids.gsis_id
        LEFT JOIN nfl.players_info p_info ON pbp."rusher_player_id" = p_info.gsis_id
        LEFT JOIN nfl.combined_schedules sch ON pbp.game_id = sch.game_id
GROUP BY pbp."rusher_player_id", p_info.first_name, p_info.last_name, pbp.season, sch."game_type";
alter materialized view nfl.player_rushing_log_season owner to than;