@echo off
TITLE PostgreSQL SSH Tunnel

echo.
echo =================================================================
echo  Starting SSH Tunnel to PostgreSQL VPS...
echo =================================================================
echo.
echo  This script forwards local port 5433 to the database on the server.
echo.
echo  IMPORTANT:
echo  - You MUST keep this window open to maintain the database connection.
echo  - Close this window to terminate the tunnel.
echo.
echo =================================================================
echo.

ssh -N -L 5433:localhost:5432 than@152.53.136.147