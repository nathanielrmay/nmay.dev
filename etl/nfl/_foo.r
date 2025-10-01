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

message("Downloading Injuries data...")
pbp_df <- nflreadr::load_injuries(cy)
message(paste("Downloaded", nrow(pbp_df), "rows of data."))
message("Writing data to table 'injuries'...")
dbWriteTable(con, DBI::Id( schema=CURRENT_SCHEMA, table="injuries" ), pbp_df, overwrite = TRUE, row.names = FALSE)
message("Successfully wrote data.")
rm(pbp_df) # Clean up memory
gc()       # Force garbage collection

# --- Final Step: Disconnect from PostgreSQL ---
dbDisconnect(con)
message("\nAll tasks complete. Disconnected from PostgreSQL.")