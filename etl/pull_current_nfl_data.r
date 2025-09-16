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
cy <- 2025
CURRENT_SCHEMA <- "nfl_current"

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
pbp_df <- nflreadr::load_pbp(cy)
message(paste("Downloaded", nrow(pbp_df), "rows of PBP data."))
message("Writing data to table 'play_by_play'...")
dbWriteTable(con, DBI::Id( schema=CURRENT_SCHEMA, table="play_by_play" ), pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading Player Stats Data ---")
pbp_df <- nflreadr::load_player_stats(cy)
message(paste("Downloaded", nrow(pbp_df), "rows of PBP data."))
message("Writing data to table 'player_stats'...")
dbWriteTable(con, DBI::Id( schema=CURRENT_SCHEMA, table="player_stats" ), pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading Team Stats Data ---")
pbp_df <- nflreadr::load_team_stats(cy)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing data to table 'team_stats'...")
dbWriteTable(con, DBI::Id( schema=CURRENT_SCHEMA, table="team_stats" ), pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading Participation data...")
pbp_df <- nflreadr::load_participation(cy)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing data to table 'participation'...")
dbWriteTable(con, DBI::Id( schema=CURRENT_SCHEMA, table="participation" ), pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading Roster Data ---")
pbp_df <- nflreadr::load_rosters(cy)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing data to table 'rosters'...")
dbWriteTable(con, DBI::Id( schema=CURRENT_SCHEMA, table="rosters" ), pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading Weekly Roster data...")
pbp_df <- nflreadr::load_rosters_weekly(cy)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing data to table 'rosters_weekly'...")
dbWriteTable(con, DBI::Id( schema=CURRENT_SCHEMA, table="rosters_weekly" ), pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading Team data...")
pbp_df <- nflreadr::load_teams(cy)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing data to table 'teams'...")
dbWriteTable(con, DBI::Id( schema=CURRENT_SCHEMA, table="teams" ), pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading Schedule data...")
pbp_df <- nflreadr::load_schedules(cy)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing data to table 'schedules'...")
dbWriteTable(con, DBI::Id( schema=CURRENT_SCHEMA, table="schedules" ), pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading Officials data...")
pbp_df <- nflreadr::load_officials(cy)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing data to table 'officials'...")
dbWriteTable(con, DBI::Id( schema=CURRENT_SCHEMA, table="officials" ), pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading Trade data...")
pbp_df <- nflreadr::load_trades(cy)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing data to table 'trades'...")
dbWriteTable(con, DBI::Id( schema=CURRENT_SCHEMA, table="trades" ), pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading Draft Picks data...")
pbp_df <- nflreadr::load_draft_picks(cy)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing data to table 'draft_picks'...")
dbWriteTable(con, DBI::Id( schema=CURRENT_SCHEMA, table="draft_picks" ), pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading Combine data...")
pbp_df <- nflreadr::load_combine(cy)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing data to table 'combine'...")
dbWriteTable(con, DBI::Id( schema=CURRENT_SCHEMA, table="combine" ), pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading Nextgen Stats data...")
pbp_df <- nflreadr::load_nextgen_stats(cy)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing data to table 'nextgen_stats'...")
dbWriteTable(con, DBI::Id( schema=CURRENT_SCHEMA, table="nextgen_stats" ), pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading Depth Charts data...")
pbp_df <- nflreadr::load_depth_charts(cy)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing data to table 'depth_charts'...")
dbWriteTable(con, DBI::Id( schema=CURRENT_SCHEMA, table="depth_charts" ), pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading Injuries data...")
pbp_df <- nflreadr::load_injuries(cy)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing data to table 'injuries'...")
dbWriteTable(con, DBI::Id( schema=CURRENT_SCHEMA, table="injuries" ), pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading Espn QBR data...")
pbp_df <- nflreadr::load_espn_qbr(cy)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing data to table 'espn_qbr'...")
dbWriteTable(con, DBI::Id( schema=CURRENT_SCHEMA, table="espn_qbr" ), pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading PFR Adv Stats data...")
pbp_df <- nflreadr::load_pfr_advstats(cy)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing data to table 'pfr_advstats'...")
dbWriteTable(con, DBI::Id( schema=CURRENT_SCHEMA, table="pfr_adv_stats" ), pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading Snap Counts data...")
pbp_df <- nflreadr::load_snap_counts(cy)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing data to table 'snapcounts'...")
dbWriteTable(con, DBI::Id( schema=CURRENT_SCHEMA, table="snap_counts" ), pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading Contract data...")
pbp_df <- nflreadr::load_contracts()
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing PBP data to PostgreSQL table 'contracts'...")
# --- FLATTEN THE DATA ---
# Use tidyr::unnest() to expand the nested 'cols' column
message("Flattening the nested 'cols' column...")
contracts_flat_df <- tidyr::unnest(pbp_df, cols = c(cols), names_sep = "_")
message(paste("Data flattened to", nrow(contracts_flat_df), "rows."))

message("Writing flattened contracts data to PostgreSQL table 'contracts'...")
dbWriteTable(con, DBI::Id( schema=CURRENT_SCHEMA, table="contracts" ), contracts_flat_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote contracts data.")
rm(pbp_df, contracts_flat_df) # Clean up memory for both data frames
gc()       # Force garbage collection


# --- Final Step: Disconnect from PostgreSQL ---
dbDisconnect(con)
message("\nAll tasks complete. Disconnected from PostgreSQL.")