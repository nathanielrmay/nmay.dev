# Development Protocol
> **CRITICAL INSTRUCTION FOR AI AGENTS:** Review this document (`docs/project.md`) with every change to the codebase. After completing a significant task or reaching a stable milestone, you MUST:
> 1. **Commit**: Stage and commit your changes with a concise, descriptive message.
> 2. **Push**: Push the changes to the remote repository (`origin master`) immediately.
> 3. **Sync Parent**: After pushing here, also perform a commit in the parent 'everything' project to sync the project's metadata.

# Repository Scope
- **Tracked**: This repository exclusively tracks the `src/` and `docs/` directories.
- **Ignored**: `.idea/`, `.gemini/`, and `.antigravity/` are explicitly ignored. These configuration files are managed and backed up by the parent **'everything'** project.

## Deployment & Testing Protocol
**CRITICAL:** The local workspace is **NOT** automatically synced to the live server (`nmay.dev`). 
The files you edit locally exist only in your workspace until the user deploys them.
If you need to run tests to accomplish a task coordinate that with the user: 
**Notify the User:** Explicitly ask the user to deploy the specific files you have modified or created.
**Wait for Confirmation:** Do not attempt to fetch the URL until the user confirms the deployment is complete.
*Failure to follow this will result in 404 errors or testing stale code.*

# Site Architecture & Developer Guide

This document outlines the architecture of the web application, its core components, and instructions for developers adding new pages or features. It is designed to serve as context for both human developers and LLM assistants.

## 1. Core Architecture

The site utilizes a custom PHP framework based on a Front Controller pattern with a Service/Dependency helper class called "Basket".

### 1.1 Front Controller (`src/index.php`)
*   **Role:** The single entry point for all dynamic requests.
*   **Responsibilities:**
    *   **Session Management:** Starts the session (`session_start`) globally if not already active.
    *   **Routing:** Determines the requested URI. Strips query parameters using `parse_url` to find matching files.
    *   **Page Inspection:** Uses `basket::inspectPage` to determine if the target page exists and if it has a backing Class.
    *   **Layout Assembly:** Orchestrates the rendering of Header, Vertical Menu, Page Content, and Footer.
    *   **404 Handling:** Routes to `src/pages/pg_404.php` if the target is invalid.

### 1.2 The Basket (`src/lib/basket.php`)
*   **Role:** The utility belt and autoloader for the application.
*   **Key Functions:**
    *   **Autoloader:** Registers a `spl_autoload_register` function to load classes from `lib/` and `pages/` based on namespace mapping. Namespace casing should be lowercase.
    *   **`inspectPage($path)`:** Includes the page file and returns captured HTML and instantiated Page Object.
    *   **`render($path, $args)`:** The preferred method for rendering partials. It `extracts` the `$args` array into local variables within the partial's scope, preventing variable leakage.
    *   **Database:** Provides connection methods (e.g., `db_web()`, `db_panal()`) to the Postgres instance.

---

## 2. File Naming Conventions (Mandatory)

To avoid class redeclaration issues and improve code organization, the following prefixes MUST be used for all new files:

### 2.1 Database Models (`db_`)
*   **Location:** `src/lib/db/models/`
*   **Naming:** `db_[table_name].php` (e.g., `db_users.php`, `db_standings.php`).
*   **Class Name:** Must match the filename (e.g., `class db_users`).

### 2.2 Partials (`pt_`)
*   **Location:** `src/pages/[section]/partial/` or `src/lib/partials/`
*   **Naming:** `pt_[name].php` (e.g., `pt_news.php`, `pt_teams.php`).
*   **Usage:** Included via `basket::render()`.

### 2.3 Pages (`pg_`)
*   **Location:** `src/pages/[section]/`
*   **Naming:** `pg_[name].php` (e.g., `pg_index.php`, `pg_teams.php`).
*   **Class Name:** Must match the filename (e.g., `class pg_teams`).

### 2.4 Interfaces (`i_`)
*   **Location:** `src/lib/contracts/` or within section-specific `lib/` folders.
*   **Naming:** `i[Name].php` (e.g., `iPage.php`, `iPartial.php`).
*   **Purpose:** Define contracts that concrete classes must implement.

### 2.5 Abstract Classes (`a_`)
*   **Location:** `src/lib/contracts/` or within section-specific `lib/` folders.
*   **Naming:** `a[Name].php` (e.g., `aPage.php`, `aPartial.php`).
*   **Purpose:** Provide base functionality for inheritance.

---

## 3. Tool Availability & Usage

The following tools are available for development and data investigation:

### 3.1 Database Tools
- **`dbhub-postgres-panal`**: Used to read sports analysis data (NBA, NCAAM, etc.) gathered by the `panal` module.
- **`dbhub-postgres-web`**: The primary database for user accounts, sessions, and site-specific administrative data. Exclusive to this project.
- **`dbhub-postgres-afg`**: Reserved for the American Football Game (AFG) section of this project. Not currently in active use.

### 3.2 System & Investigation Tools
*   **`run_shell_command`**: Execute PowerShell commands. Used for moving/renaming files, checking system state, or running CLI tools.
*   **`web_fetch`**: Fetch HTML content or source from live URLs. Essential for verifying how pages render on the live site (`https://nmay.dev`).
*   **`search_file_content`**: FAST recursive search (ripgrep). Use this to find class usages, variable definitions, or specific strings across the codebase.
*   **`glob`**: Quickly find files matching specific patterns.
*   **`read_file` / `write_file` / `replace`**: Primary tools for reading and modifying the codebase. `replace` requires exact literal matches including whitespace.

---

## 4. Current Directory Overview

```text
X:\projects\dev\public_web\
├── src\
│   ├── index.php           # Front Controller
│   ├── .htaccess           # Apache Routing Rules
│   ├── lib\
│   │   ├── api\            # API client classes (e.g. `aApiClient.php`, `placesApiClient.php`)
│   │   ├── basket.php      # Autoloader, Page Inspector, & Render Helper
│   │   ├── content.php     # Rendering Wrapper
│   │   ├── nmay.css        # Site css rules
│   │   ├── contracts\      # Interfaces (i_*) and Abstract classes (a_*)
│   │   │   ├── iPage.php
│   │   │   ├── aPage.php
│   │   │   ├── iPartial.php
│   │   │   └── aPartial.php
│   │   ├── db\             # Database connection & Models (db_*)
│   │   │   └── models\
│   │   │       ├── web\    # Internal web db models (db_users)
│   │   │       ├── panal\  # External sports db models
│   │   │       └── wrv\    # WRV database models
│   │   └── partials\       # Global layout fragments
│   └── pages\   
│       ├── pg_404.php      # Error Page
│       ├── pg_index.php    # Home Page content
│       ├── account\        # User Account pages (pg_*)
│       └── [sections]      # Content section with different subject matters (e.g., wrv, sports_anals)
│                   ├── aAbstractC  # Many sections will have their own abstract class used to replace the menu's or to tell the controller to do something specific for this section
│                   ├── lib\
│                   │   └── partials\ # A content section can have its own partials directory (e.g., pt_header.php)

---

## 5. Styling & Scoping Conventions (Mandatory)

To prevent global CSS (`nmay.css`) from interfering with section-specific themes, the following namespacing pattern MUST be followed.

### 5.1 Body Scoping (`getBodyClass`)
Every page class (inheriting from `aPage`) can implement `getBodyClass()`. This string is applied directly to the `<body>` tag in `index.php`.
*   **Mapping:** The return value MUST unambiguously map to the theme's primary CSS file (e.g., `newspapercss` maps to `newspaper.css`).
*   **Purpose:** Provides a high-level CSS namespace (e.g., `body.newspapercss { ... }`).

### 5.2 CSS Prefixing
All rules in section-specific stylesheets MUST be prefixed with the body class to ensure they override global defaults.
*   **Global Overrides:** `.newspapercss .header { ... }`
*   **Partial Scoping:** For rules targeting a specific partial (e.g., `pt_header.php`), use the partial name as an additional scope:
    *   `.newspapercss .pt-header .newspaper-nav { ... }`
*   **HTML Structure:** Partials using this scoping SHOULD wrap their entire output in a div named after the partial (e.g., `<div class="pt-header">...</div>`).
```

```