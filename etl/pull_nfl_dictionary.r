# --- 0. Setup: Load Libraries and Define PostgreSQL Connection ---
library(nflverse)
library(DBI)
library(RPostgres)
library(dplyr) # For data manipulation

# --- Your PostgreSQL Connection Details ---
PG_HOST <- "localhost"
PG_PORT <- 5432
PG_DBNAME <- "proper_analysis"
PG_USER <- "than"
PG_PASSWORD <- "Eattherich3537!"

DICTIONARY_SCHEMA <- "nfl_dictionary"

# --- Connect to PostgreSQL ---
message("Connecting to PostgreSQL database...")
con <- dbConnect(RPostgres::Postgres(),
                 host = PG_HOST,
                 port = PG_PORT,
                 dbname = PG_DBNAME,
                 user = PG_USER,
                 password = PG_PASSWORD)
message("Connection successful.")

message(paste("Ensuring schema '", DICTIONARY_SCHEMA, "' exists..."))
dbExecute(con, paste("CREATE SCHEMA IF NOT EXISTS", DICTIONARY_SCHEMA))

message("Downloading PBP dictionary...")
pbp_dict_df <- nflreadr::dictionary_pbp
message(paste("Downloaded", nrow(pbp_dict_df), "dictionary entries."))
message("Writing PBP dictionary to PostgreSQL table 'dictionary_play_by_play'...")
dbWriteTable(con, DBI::Id( schema=DICTIONARY_SCHEMA, table="play_by_play" ), pbp_dict_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote PBP dictionary.")
rm(pbp_dict_df) # Clean up memory

message("Downloading Player Stats dictionary...")
pbp_dict_df <- nflreadr::dictionary_player_stats
message(paste("Downloaded", nrow(pbp_dict_df), "dictionary entries."))
message("Writing Player Stats dictionary to PostgreSQL table 'dictionary_player_stats'...")
dbWriteTable(con, DBI::Id( schema=DICTIONARY_SCHEMA, table="player_stats" ), pbp_dict_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote Player Stats dictionary.")
rm(pbp_dict_df) # Clean up memory

message("Downloading Team Stats dictionary...")
pbp_dict_df <- nflreadr::dictionary_team_stats
message(paste("Downloaded", nrow(pbp_dict_df), "dictionary entries."))
message("Writing Team Stats dictionary to PostgreSQL table 'dictionary_team_stats'...")
dbWriteTable(con, DBI::Id( schema=DICTIONARY_SCHEMA, table="team_stats" ), pbp_dict_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote Team Stats dictionary.")
rm(pbp_dict_df) # Clean up memory

message("Downloading Participation dictionary...")
pbp_dict_df <- nflreadr::dictionary_participation
message(paste("Downloaded", nrow(pbp_dict_df), "dictionary entries."))
message("Writing Participation dictionary to PostgreSQL table 'dictionary_participation'...")
dbWriteTable(con, DBI::Id( schema=DICTIONARY_SCHEMA, table="participation" ), pbp_dict_df, overwrite = TRUE, row.names = FALSE)
message("Successfully Participation dictionary.")
rm(pbp_dict_df) # Clean up memory

message("Downloading Rosters dictionary...")
pbp_dict_df <- nflreadr::dictionary_rosters
message(paste("Downloaded", nrow(pbp_dict_df), "dictionary entries."))
message("Writing Rosters dictionary to PostgreSQL table 'dictionary_rosters'...")
dbWriteTable(con, DBI::Id( schema=DICTIONARY_SCHEMA, table="rosters" ), pbp_dict_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote Rosters dictionary.")
rm(pbp_dict_df) # Clean up memory

message("Downloading Schedules dictionary...")
pbp_dict_df <- nflreadr::dictionary_schedules
message(paste("Downloaded", nrow(pbp_dict_df), "dictionary entries."))
message("Writing Schedules dictionary to PostgreSQL table 'dictionary_schedules'...")
dbWriteTable(con, DBI::Id( schema=DICTIONARY_SCHEMA, table="schedules" ), pbp_dict_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote Schedules dictionary.")
rm(pbp_dict_df) # Clean up memory

message("Downloading Trades dictionary...")
pbp_dict_df <- nflreadr::dictionary_trades
message(paste("Downloaded", nrow(pbp_dict_df), "dictionary entries."))
message("Writing Trades dictionary to PostgreSQL table 'dictionary_trades'...")
dbWriteTable(con, DBI::Id( schema=DICTIONARY_SCHEMA, table="trades" ), pbp_dict_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote Trades dictionary.")
rm(pbp_dict_df) # Clean up memory

message("Downloading Draft Picks dictionary...")
pbp_dict_df <- nflreadr::dictionary_draft_picks
message(paste("Downloaded", nrow(pbp_dict_df), "dictionary entries."))
message("Writing Draft Picks dictionary to PostgreSQL table 'dictionary_draft_picks'...")
dbWriteTable(con, DBI::Id( schema=DICTIONARY_SCHEMA, table="draft_picks" ), pbp_dict_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote Draft Picks dictionary.")
rm(pbp_dict_df) # Clean up memory

message("Downloading Combine dictionary...")
pbp_dict_df <- nflreadr::dictionary_combine
message(paste("Downloaded", nrow(pbp_dict_df), "dictionary entries."))
message("Writing Combine dictionary to PostgreSQL table 'dictionary_combine'...")
dbWriteTable(con, DBI::Id( schema=DICTIONARY_SCHEMA, table="combine" ), pbp_dict_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote Combine dictionary.")
rm(pbp_dict_df) # Clean up memory

message("Downloading Nextgen Stats dictionary...")
pbp_dict_df <- nflreadr::dictionary_nextgen_stats
message(paste("Downloaded", nrow(pbp_dict_df), "dictionary entries."))
message("Writing Nextgen Stats dictionary to PostgreSQL table 'dictionary_nextgen_stats'...")
dbWriteTable(con, DBI::Id( schema=DICTIONARY_SCHEMA, table="nextgen_stats" ), pbp_dict_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote Nextgen Stats dictionary.")
rm(pbp_dict_df) # Clean up memory

message("Downloading Depth Charts dictionary...")
pbp_dict_df <- nflreadr::dictionary_depth_charts
message(paste("Downloaded", nrow(pbp_dict_df), "dictionary entries."))
message("Writing Depth Charts dictionary to PostgreSQL table 'dictionary_depth_charts'...")
dbWriteTable(con, DBI::Id( schema=DICTIONARY_SCHEMA, table="depth_charts" ), pbp_dict_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote Depth Charts dictionary.")
rm(pbp_dict_df) # Clean up memory

message("Downloading Injuries dictionary...")
pbp_dict_df <- nflreadr::dictionary_injuries
message(paste("Downloaded", nrow(pbp_dict_df), "dictionary entries."))
message("Writing Injuries dictionary to PostgreSQL table 'dictionary_injuries'...")
dbWriteTable(con, DBI::Id( schema=DICTIONARY_SCHEMA, table="injuries" ), pbp_dict_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote Injuries dictionary.")
rm(pbp_dict_df) # Clean up memory

message("Downloading ESPN Qbr dictionary...")
pbp_dict_df <- nflreadr::dictionary_espn_qbr
message(paste("Downloaded", nrow(pbp_dict_df), "dictionary entries."))
message("Writing ESPN Qbr dictionary to PostgreSQL table 'dictionary_espn_qbr'...")
dbWriteTable(con, DBI::Id( schema=DICTIONARY_SCHEMA, table="espn_qbr" ), pbp_dict_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote ESPN Qbr dictionary.")
rm(pbp_dict_df) # Clean up memory

message("Downloading PFR Passing dictionary...")
pbp_dict_df <- nflreadr::dictionary_pfr_passing
message(paste("Downloaded", nrow(pbp_dict_df), "dictionary entries."))
message("Writing PFR Passing dictionary to PostgreSQL table 'dictionary_pfr_passing'...")
dbWriteTable(con, DBI::Id( schema=DICTIONARY_SCHEMA, table="pfr_passing" ), pbp_dict_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote PFR Passing dictionary.")
rm(pbp_dict_df) # Clean up memory

message("Downloading Snap Counts dictionary...")
pbp_dict_df <- nflreadr::dictionary_snap_counts
message(paste("Downloaded", nrow(pbp_dict_df), "dictionary entries."))
message("Writing Snap Counts dictionary to PostgreSQL table 'dictionary_snap_counts'...")
dbWriteTable(con, DBI::Id( schema=DICTIONARY_SCHEMA, table="snap_counts" ), pbp_dict_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote Snap Counts dictionary.")
rm(pbp_dict_df) # Clean up memory

message("Downloading Contracts dictionary...")
pbp_dict_df <- nflreadr::dictionary_contracts
message(paste("Downloaded", nrow(pbp_dict_df), "dictionary entries."))
message("Writing Contracts dictionary to PostgreSQL table 'dictionary_contracts'...")
dbWriteTable(con, DBI::Id( schema=DICTIONARY_SCHEMA, table="contracts" ), pbp_dict_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote Contracts dictionary.")
rm(pbp_dict_df) # Clean up memory

# --- Final Step: Disconnect from PostgreSQL ---
dbDisconnect(con)
message("\nAll tasks complete. Disconnected from PostgreSQL.")
