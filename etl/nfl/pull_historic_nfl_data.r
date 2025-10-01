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

# --- Define Seasons ---
last_historic_season <- 2024
HISTORIC_SCHEMA = "nfl_historic"

# --- Connect to PostgreSQL ---
message("Connecting to PostgreSQL database...")
con <- dbConnect(RPostgres::Postgres(),
                 host = PG_HOST,
                 port = PG_PORT,
                 dbname = PG_DBNAME,
                 user = PG_USER,
                 password = PG_PASSWORD)
message("Connection successful.")

message(paste("Ensuring schema '", HISTORIC_SCHEMA, "' exists..."))
dbExecute(con, paste("CREATE SCHEMA IF NOT EXISTS", HISTORIC_SCHEMA))

message("Downloading all play-by-play data...")
pbp_df <- nflreadr::load_pbp(1999:last_historic_season)
message(paste("Downloaded", nrow(pbp_df), "rows of PBP data."))
message("Writing data to table 'play_by_play'...")
dbWriteTable(con, DBI::Id( schema=HISTORIC_SCHEMA, table="play_by_play" ), pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("\n--- Processing Player Stats Data ---")
pbp_df <- nflreadr::load_player_stats(1999:last_historic_season)
message(paste("Downloaded", nrow(pbp_df), "rows of PBP data."))
message("Writing data to table 'player_stats'...")
dbWriteTable(con, DBI::Id( schema=HISTORIC_SCHEMA, table="player_stats" ), pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading all historical Team Stats data...")
pbp_df <- nflreadr::load_team_stats(1999:last_historic_season)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing data to table 'team_stats'...")
dbWriteTable(con, DBI::Id( schema=HISTORIC_SCHEMA, table="team_stats" ), pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading all historical Participation data...")
pbp_df <- nflreadr::load_participation(2016:last_historic_season)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing data to table 'participation'...")
dbWriteTable(con, DBI::Id( schema=HISTORIC_SCHEMA, table="participation" ), pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading all historical Roster data...")
pbp_df <- nflreadr::load_rosters(1999:last_historic_season)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing data to table 'rosters'...")
dbWriteTable(con, DBI::Id( schema=HISTORIC_SCHEMA, table="rosters" ), pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading all historical Roster Weekly data...")
pbp_df <- nflreadr::load_rosters_weekly(2002:last_historic_season)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing data to table 'rosters_weekly'...")
dbWriteTable(con, DBI::Id( schema=HISTORIC_SCHEMA, table="rosters_weekly" ), pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading all historical Team data...")
pbp_df <- nflreadr::load_teams(1999:last_historic_season)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing data to table 'teams'...")
dbWriteTable(con, DBI::Id( schema=HISTORIC_SCHEMA, table="teams" ), pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading all historical Schedule data...")
pbp_df <- nflreadr::load_schedules(1999:last_historic_season)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing data to table 'schedules'...")
dbWriteTable(con, DBI::Id( schema=HISTORIC_SCHEMA, table="schedules" ), pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading all historical Official data...")
pbp_df <- nflreadr::load_officials(2015:last_historic_season)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing data to table 'officials'...")
dbWriteTable(con, DBI::Id( schema=HISTORIC_SCHEMA, table="officials" ), pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading all historical Trades data...")
pbp_df <- nflreadr::load_trades(1999:last_historic_season)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing data to table 'trades'...")
dbWriteTable(con, DBI::Id( schema=HISTORIC_SCHEMA, table="trades" ), pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading all historical Draft Picks data...")
pbp_df <- nflreadr::load_draft_picks(1999:last_historic_season)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing data to table 'draft_picks'...")
dbWriteTable(con, DBI::Id( schema=HISTORIC_SCHEMA, table="draft_picks" ), pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading all historical Combine data...")
pbp_df <- nflreadr::load_combine(1999:last_historic_season)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing data to table 'combine'...")
dbWriteTable(con, DBI::Id( schema=HISTORIC_SCHEMA, table="combine" ), pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading all historical Nextgen Stats data...")
pbp_df <- nflreadr::load_nextgen_stats(2016:last_historic_season)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing data to table 'nextgen_stats'...")
dbWriteTable(con, DBI::Id( schema=HISTORIC_SCHEMA, table="nextgen_stats" ), pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading all historical Depth Chart data...")
pbp_df <- nflreadr::load_depth_charts(2001:last_historic_season)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing data to table 'depth_charts'...")
dbWriteTable(con, DBI::Id( schema=HISTORIC_SCHEMA, table="depth_charts" ), pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading all historical Injuries data...")
pbp_df <- nflreadr::load_injuries(2009:last_historic_season)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing data to table 'injuries'...")
dbWriteTable(con, DBI::Id( schema=HISTORIC_SCHEMA, table="injuries" ), pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading all historical ESPN Qbr data...")
pbp_df <- nflreadr::load_espn_qbr(2006:last_historic_season)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing data to table 'espn_qbr'...")
dbWriteTable(con, DBI::Id( schema=HISTORIC_SCHEMA, table="espn_qbr" ), pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading all historical Pfr Adv Stats data...")
pbp_df <- nflreadr::load_pfr_advstats(2018:last_historic_season)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing data to table 'pfr_advstats'...")
dbWriteTable(con, DBI::Id( schema=HISTORIC_SCHEMA, table="pfr_adv_stats" ), pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading all historical Snap Count data...")
pbp_df <- nflreadr::load_snap_counts(2012:last_historic_season)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing data to table 'snapcounts'...")
dbWriteTable(con, DBI::Id( schema=HISTORIC_SCHEMA, table="snap_counts" ), pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading all historical Contracts data...")
pbp_df <- nflreadr::load_contracts()
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing data to table 'contracts'...")
# --- FLATTEN THE DATA ---
# Use tidyr::unnest() to expand the nested 'cols' column
message("Flattening the nested 'cols' column...")
contracts_flat_df <- tidyr::unnest(pbp_df, cols = c(cols), names_sep = "_")
message(paste("Data flattened to", nrow(contracts_flat_df), "rows."))

message("Writing flattened contracts data to PostgreSQL table 'contracts'...")
dbWriteTable(con, DBI::Id( schema=HISTORIC_SCHEMA, table="contracts" ), contracts_flat_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df, contracts_flat_df) # Clean up memory for both data frames
gc()       # Force garbage collection

# --- Final Step: Disconnect from PostgreSQL ---
dbDisconnect(con)
message("\nAll tasks complete. Disconnected from PostgreSQL.")