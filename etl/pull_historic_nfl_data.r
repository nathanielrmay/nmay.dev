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
historical_seasons <- 1999:2024

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
message("Downloading all historical play-by-play data...")
pbp_df <- nflreadr::load_pbp(1999:2024)
message(paste("Downloaded", nrow(pbp_df), "rows of PBP data."))
message("Writing PBP data to PostgreSQL table 'play_by_play'...")
dbWriteTable(con, "nfl_historic.play_by_play", pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote PBP data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("\n--- Processing Play-by-Play Dictionary ---")
message("Downloading PBP dictionary...")
pbp_dict_df <- nflreadr::dictionary_pbp
message(paste("Downloaded", nrow(pbp_dict_df), "dictionary entries."))
message("Writing PBP dictionary to PostgreSQL table 'dictionary_play_by_play'...")
dbWriteTable(con, "nfl_historic.dict_play_by_play", pbp_dict_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote PBP dictionary.")
rm(pbp_dict_df) # Clean up memory

message("\n--- Processing Player Stats Data ---")
pbp_df <- nflreadr::load_player_stats(1999:2024)
message(paste("Downloaded", nrow(pbp_df), "rows of PBP data."))
message("Writing PBP data to PostgreSQL table 'player_stats'...")
dbWriteTable(con, "nfl_historic.player_stats", pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote PBP data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("\n--- Processing Player Stats Dictionary ---")
message("Downloading PBP dictionary...")
pbp_dict_df <- nflreadr::dictionary_player_stats
message(paste("Downloaded", nrow(pbp_dict_df), "dictionary entries."))
message("Writing PBP dictionary to PostgreSQL table 'dictionary_player_stats'...")
dbWriteTable(con, "nfl_historic.dict_player_stats", pbp_dict_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote PBP dictionary.")
rm(pbp_dict_df) # Clean up memory

message("\n--- Processing Data ---")
message("Downloading all historical Player Stats data...")
pbp_df <- nflreadr::load_team_stats(1999:2024)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing PBP data to PostgreSQL table 'team_stats'...")
dbWriteTable(con, "nfl_historic.team_stats", pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("\n--- Processing Dictionary ---")
message("Downloading PBP dictionary...")
pbp_dict_df <- nflreadr::dictionary_team_stats
message(paste("Downloaded", nrow(pbp_dict_df), "dictionary entries."))
message("Writing PBP dictionary to PostgreSQL table 'dictionary_team_stats'...")
dbWriteTable(con, "nfl_historic.dictionary_team_stats", pbp_dict_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote dictionary.")
rm(pbp_dict_df) # Clean up memory

message("\n--- Processing Data ---")
message("Downloading all historical Player Stats data...")
pbp_df <- nflreadr::load_participation(2016:2024)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing PBP data to PostgreSQL table 'participation'...")
dbWriteTable(con, "nfl_historic.participation", pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("\n--- Processing Dictionary ---")
message("Downloading PBP dictionary...")
pbp_dict_df <- nflreadr::dictionary_participation
message(paste("Downloaded", nrow(pbp_dict_df), "dictionary entries."))
message("Writing PBP dictionary to PostgreSQL table 'dictionary_participation'...")
dbWriteTable(con, "nfl_historic.dictionary_participation", pbp_dict_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote dictionary.")
rm(pbp_dict_df) # Clean up memory

message("\n--- Processing Data ---")
message("Downloading all historical Player Stats data...")
pbp_df <- nflreadr::load_rosters(1999:2024)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing PBP data to PostgreSQL table 'rosters'...")
dbWriteTable(con, "nfl_historic.rosters", pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("\n--- Processing Dictionary ---")
message("Downloading PBP dictionary...")
pbp_dict_df <- nflreadr::dictionary_rosters
message(paste("Downloaded", nrow(pbp_dict_df), "dictionary entries."))
message("Writing PBP dictionary to PostgreSQL table 'dictionary_rosters'...")
dbWriteTable(con, "nfl_historic.dictionary_rosters", pbp_dict_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote dictionary.")
rm(pbp_dict_df) # Clean up memory

message("\n--- Processing Data ---")
message("Downloading all historical Player Stats data...")
pbp_df <- nflreadr::load_rosters_weekly(2002:2024)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing PBP data to PostgreSQL table 'rosters_weekly'...")
dbWriteTable(con, "nfl_historic.rosters_weekly", pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("\n--- Processing Data ---")
message("Downloading all historical Player Stats data...")
pbp_df <- nflreadr::load_teams(1999:2024)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing PBP data to PostgreSQL table 'teams'...")
dbWriteTable(con, "nfl_historic.teams", pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("\n--- Processing Data ---")
message("Downloading all historical Player Stats data...")
pbp_df <- nflreadr::load_schedules(1999:2024)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing PBP data to PostgreSQL table 'schedules'...")
dbWriteTable(con, "nfl_historic.schedules", pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("\n--- Processing Dictionary ---")
message("Downloading PBP dictionary...")
pbp_dict_df <- nflreadr::dictionary_schedules
message(paste("Downloaded", nrow(pbp_dict_df), "dictionary entries."))
message("Writing PBP dictionary to PostgreSQL table 'dictionary_schedules'...")
dbWriteTable(con, "nfl_historic.dictionary_schedules", pbp_dict_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote dictionary.")
rm(pbp_dict_df) # Clean up memory

message("\n--- Processing Data ---")
message("Downloading all historical Player Stats data...")
pbp_df <- nflreadr::load_officials(2015:2024)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing PBP data to PostgreSQL table 'officials'...")
dbWriteTable(con, "nfl_historic.officials", pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("\n--- Processing Data ---")
message("Downloading all historical Player Stats data...")
pbp_df <- nflreadr::load_trades(1999:2024)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing PBP data to PostgreSQL table 'trades'...")
dbWriteTable(con, "nfl_historic.trades", pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("\n--- Processing Dictionary ---")
message("Downloading PBP dictionary...")
pbp_dict_df <- nflreadr::dictionary_trades
message(paste("Downloaded", nrow(pbp_dict_df), "dictionary entries."))
message("Writing PBP dictionary to PostgreSQL table 'dictionary_trades'...")
dbWriteTable(con, "nfl_historic.dictionary_trades", pbp_dict_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote dictionary.")
rm(pbp_dict_df) # Clean up memory

message("\n--- Processing Data ---")
message("Downloading all historical Player Stats data...")
pbp_df <- nflreadr::load_draft_picks(1999:2024)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing PBP data to PostgreSQL table 'draft_picks'...")
dbWriteTable(con, "nfl_historic.draft_picks", pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("\n--- Processing Dictionary ---")
message("Downloading PBP dictionary...")
pbp_dict_df <- nflreadr::dictionary_draft_picks
message(paste("Downloaded", nrow(pbp_dict_df), "dictionary entries."))
message("Writing PBP dictionary to PostgreSQL table 'dictionary_draft_picks'...")
dbWriteTable(con, "nfl_historic.dictionary_draft_picks", pbp_dict_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote dictionary.")
rm(pbp_dict_df) # Clean up memory

message("\n--- Processing Data ---")
message("Downloading all historical Player Stats data...")
pbp_df <- nflreadr::load_combine(1999:2024)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing PBP data to PostgreSQL table 'combine'...")
dbWriteTable(con, "cnfl_historic.ombine", pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("\n--- Processing Dictionary ---")
message("Downloading PBP dictionary...")
pbp_dict_df <- nflreadr::dictionary_combine
message(paste("Downloaded", nrow(pbp_dict_df), "dictionary entries."))
message("Writing PBP dictionary to PostgreSQL table 'dictionary_combine'...")
dbWriteTable(con, "nfl_historic.dictionary_combine", pbp_dict_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote dictionary.")
rm(pbp_dict_df) # Clean up memory

message("\n--- Processing Data ---")
message("Downloading all historical Player Stats data...")
pbp_df <- nflreadr::load_nextgen_stats(2016:2024)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing PBP data to PostgreSQL table 'nextgen_stats'...")
dbWriteTable(con, "nfl_historic.nextgen_stats", pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("\n--- Processing Dictionary ---")
message("Downloading PBP dictionary...")
pbp_dict_df <- nflreadr::dictionary_nextgen_stats
message(paste("Downloaded", nrow(pbp_dict_df), "dictionary entries."))
message("Writing PBP dictionary to PostgreSQL table 'dictionary_nextgen_stats'...")
dbWriteTable(con, "nfl_historic.dictionary_nextgen_stats", pbp_dict_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote dictionary.")
rm(pbp_dict_df) # Clean up memory

message("\n--- Processing Data ---")
message("Downloading all historical Player Stats data...")
pbp_df <- nflreadr::load_depth_charts(2001:2024)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing PBP data to PostgreSQL table 'depth_charts'...")
dbWriteTable(con, "nfl_historic.depth_charts", pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("\n--- Processing Dictionary ---")
message("Downloading PBP dictionary...")
pbp_dict_df <- nflreadr::dictionary_depth_charts
message(paste("Downloaded", nrow(pbp_dict_df), "dictionary entries."))
message("Writing PBP dictionary to PostgreSQL table 'dictionary_depth_charts'...")
dbWriteTable(con, "nfl_historic.dictionary_depth_charts", pbp_dict_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote dictionary.")
rm(pbp_dict_df) # Clean up memory

message("\n--- Processing Data ---")
message("Downloading all historical Player Stats data...")
pbp_df <- nflreadr::load_injuries(2009:2024)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing PBP data to PostgreSQL table 'injuries'...")
dbWriteTable(con, "nfl_historic.injuries", pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("\n--- Processing Dictionary ---")
message("Downloading PBP dictionary...")
pbp_dict_df <- nflreadr::dictionary_injuries
message(paste("Downloaded", nrow(pbp_dict_df), "dictionary entries."))
message("Writing PBP dictionary to PostgreSQL table 'dictionary_injuries'...")
dbWriteTable(con, "nfl_historic.dictionary_injuries", pbp_dict_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote dictionary.")
rm(pbp_dict_df) # Clean up memory

message("\n--- Processing Data ---")
message("Downloading all historical Player Stats data...")
pbp_df <- nflreadr::load_espn_qbr(2006:2024)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing PBP data to PostgreSQL table 'espn_qbr'...")
dbWriteTable(con, "nfl_historic.espn_qbr", pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("\n--- Processing Dictionary ---")
message("Downloading PBP dictionary...")
pbp_dict_df <- nflreadr::dictionary_espn_qbr
message(paste("Downloaded", nrow(pbp_dict_df), "dictionary entries."))
message("Writing PBP dictionary to PostgreSQL table 'dictionary_espn_qbr'...")
dbWriteTable(con, "nfl_historic.dictionary_espn_qbr", pbp_dict_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote dictionary.")
rm(pbp_dict_df) # Clean up memory

message("\n--- Processing Data ---")
message("Downloading all historical Player Stats data...")
pbp_df <- nflreadr::load_pfr_advstats(2018:2024)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing PBP data to PostgreSQL table 'pfr_advstats'...")
dbWriteTable(con, "nfl_historic.pfr_adv_stats", pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("\n--- Processing Dictionary ---")
message("Downloading PBP dictionary...")
pbp_dict_df <- nflreadr::dictionary_pfr_passing
message(paste("Downloaded", nrow(pbp_dict_df), "dictionary entries."))
message("Writing PBP dictionary to PostgreSQL table 'dictionary_pfr_passing'...")
dbWriteTable(con, "nfl_historic.dictionary_pfr_passing", pbp_dict_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote dictionary.")
rm(pbp_dict_df) # Clean up memory

message("\n--- Processing Data ---")
message("Downloading all historical Player Stats data...")
pbp_df <- nflreadr::load_snap_counts(2012:2024)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing PBP data to PostgreSQL table 'snapcounts'...")
dbWriteTable(con, "nfl_historic.snap_counts", pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

message("\n--- Processing Dictionary ---")
message("Downloading PBP dictionary...")
pbp_dict_df <- nflreadr::dictionary_snap_counts
message(paste("Downloaded", nrow(pbp_dict_df), "dictionary entries."))
message("Writing PBP dictionary to PostgreSQL table 'dictionary_snap_counts'...")
dbWriteTable(con, "nfl_historic.dictionary_snap_counts", pbp_dict_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote dictionary.")
rm(pbp_dict_df) # Clean up memory

message("\n--- Processing Data ---")
message("Downloading all historical Player Stats data...")
pbp_df <- nflreadr::load_contracts()
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing PBP data to PostgreSQL table 'contracts'...")
# --- FLATTEN THE DATA ---
# Use tidyr::unnest() to expand the nested 'cols' column
message("Flattening the nested 'cols' column...")
contracts_flat_df <- tidyr::unnest(pbp_df, cols = c(cols), names_sep = "_")
message(paste("Data flattened to", nrow(contracts_flat_df), "rows."))

message("Writing flattened contracts data to PostgreSQL table 'contracts'...")
dbWriteTable(con, "contracts", contracts_flat_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote contracts data.")
rm(pbp_df, contracts_flat_df) # Clean up memory for both data frames
gc()       # Force garbage collection

message("\n--- Processing Dictionary ---")
message("Downloading PBP dictionary...")
pbp_dict_df <- nflreadr::dictionary_contracts
message(paste("Downloaded", nrow(pbp_dict_df), "dictionary entries."))
message("Writing PBP dictionary to PostgreSQL table 'dictionary_contracts'...")
dbWriteTable(con, "nfl_historic.dictionary_contracts", pbp_dict_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote dictionary.")
rm(pbp_dict_df) # Clean up memory


# --- Final Step: Disconnect from PostgreSQL ---
dbDisconnect(con)
message("\nAll tasks complete. Disconnected from PostgreSQL.")