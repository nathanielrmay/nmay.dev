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

# --- Connect to PostgreSQL ---
message("Connecting to PostgreSQL database...")
con <- dbConnect(RPostgres::Postgres(),
                 host = PG_HOST,
                 port = PG_PORT,
                 dbname = PG_DBNAME,
                 user = PG_USER,
                 password = PG_PASSWORD)
message("Connection successful.")

message("\n--- Processing Play-by-Play Data ---")
pbp_df <- nflreadr::load_pbp(cy)
message(paste("Downloaded", nrow(pbp_df), "rows of PBP data."))
message("Writing PBP data to PostgreSQL table 'play_by_play'...")
dbWriteTable(con, "nfl_current.play_by_play", pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote PBP data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("\n--- Processing Player Stats Data ---")
pbp_df <- nflreadr::load_player_stats(cy)
message(paste("Downloaded", nrow(pbp_df), "rows of PBP data."))
message("Writing PBP data to PostgreSQL table 'player_stats'...")
dbWriteTable(con, "nfl_current.player_stats", pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote PBP data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("\n--- Processing Team Stats Data ---")
pbp_df <- nflreadr::load_team_stats(cy)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing PBP data to PostgreSQL table 'team_stats'...")
dbWriteTable(con, "nfl_current.team_stats", pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading Participation data...")
pbp_df <- nflreadr::load_participation(cy)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing PBP data to PostgreSQL table 'participation'...")
dbWriteTable(con, "nfl_current.participation", pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("\n--- Processing Roster Data ---")
pbp_df <- nflreadr::load_rosters(cy)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing PBP data to PostgreSQL table 'rosters'...")
dbWriteTable(con, "nfl_current.rosters", pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading Weekly Roster data...")
pbp_df <- nflreadr::load_rosters_weekly(cy)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing PBP data to PostgreSQL table 'rosters_weekly'...")
dbWriteTable(con, "nfl_current.rosters_weekly", pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading Team data...")
pbp_df <- nflreadr::load_teams(cy)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing PBP data to PostgreSQL table 'teams'...")
dbWriteTable(con, "nfl_current.teams", pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading Schedule data...")
pbp_df <- nflreadr::load_schedules(cy)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing PBP data to PostgreSQL table 'schedules'...")
dbWriteTable(con, "nfl_current.schedules", pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading Officials data...")
pbp_df <- nflreadr::load_officials(cy)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing PBP data to PostgreSQL table 'officials'...")
dbWriteTable(con, "nfl_current.officials", pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading Trade data...")
pbp_df <- nflreadr::load_trades(cy)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing PBP data to PostgreSQL table 'trades'...")
dbWriteTable(con, "nfl_current.trades", pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading Draft Picks data...")
pbp_df <- nflreadr::load_draft_picks(cy)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing PBP data to PostgreSQL table 'draft_picks'...")
dbWriteTable(con, "nfl_current.draft_picks", pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading Combine data...")
pbp_df <- nflreadr::load_combine(cy)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing PBP data to PostgreSQL table 'combine'...")
dbWriteTable(con, "nfl_current.combine", pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading Nextgen Stats data...")
pbp_df <- nflreadr::load_nextgen_stats(cy)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing PBP data to PostgreSQL table 'nextgen_stats'...")
dbWriteTable(con, "nfl_current.nextgen_stats", pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading Depth Charts data...")
pbp_df <- nflreadr::load_depth_charts(cy)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing PBP data to PostgreSQL table 'depth_charts'...")
dbWriteTable(con, "nfl_current.depth_charts", pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading Injuries data...")
pbp_df <- nflreadr::load_injuries(cy)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing PBP data to PostgreSQL table 'injuries'...")
dbWriteTable(con, "nfl_current.injuries", pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading Espn QBR data...")
pbp_df <- nflreadr::load_espn_qbr(cy)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing PBP data to PostgreSQL table 'espn_qbr'...")
dbWriteTable(con, "nfl_current.espn_qbr", pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading PFR Adv Stats data...")
pbp_df <- nflreadr::load_pfr_advstats(cy)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing PBP data to PostgreSQL table 'pfr_adv_stats'...")
dbWriteTable(con, "nfl_current.pfr_adv_stats", pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("Downloading Snap Counts data...")
pbp_df <- nflreadr::load_snap_counts(cy)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing PBP data to PostgreSQL table 'snap_counts'...")
dbWriteTable(con, "nfl_current.snap_counts", pbp_df, overwrite = TRUE, row.names = FALSE)
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
dbWriteTable(con, "nfl_current.contracts", contracts_flat_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote contracts data.")
rm(pbp_df, contracts_flat_df) # Clean up memory for both data frames
gc()       # Force garbage collection


# --- Final Step: Disconnect from PostgreSQL ---
dbDisconnect(con)
message("\nAll tasks complete. Disconnected from PostgreSQL.")