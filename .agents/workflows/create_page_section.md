---
description: How to create a new page section in this project
---

When creating a new major page section (like `wrv` or `about` or `sports_anals`), follow these structural conventions:

1. **Create the section directory**: 
   `src/pages/[section-name]`
   and `src/pages/[section-name]/lib/partials`

2. **Create the Abstract Base Page**:
   Create `src/pages/[section-name]/a[Section]Page.php`.
   It MUST extend `lib\contracts\aPage` and provide:
   - `__construct()`: load section-specific CSS via `$this->addCss(...)`
   - `getVerticalMenu()`: return the partial path for the side menu
   - `getHeader()`: return the partial path for the header
   - `getBodyClass()`: return a unique CSS class string for this section

3. **Create the Entry Page(s)**:
   Create `src/pages/[section-name]/pg_index.php`.
   It MUST extend your abstract base page (`a[Section]Page.php`).
   Override `getPageTitle()` to return the desired title.

4. **Create the Required Partials**:
   - `src/pages/[section-name]/lib/partials/pt_header.php`: Ensure it includes the `.menu-toggle` button for mobile/responsive behavior and relevant navigation links.
   - `src/pages/[section-name]/lib/partials/pt_vertical_menu.php`: Contains the `<ul>` structure for side navigation.

5. **API & Database Integration (If Applicable)**:
   - External APIs: Create an API client in `src/lib/api/[service]/` extending `aApiClient.php`.
   - Databases models: Create standard PDO classes in `src/lib/db/models/[section-name]/`. Use `<table_name>.php` (e.g., `db_users.php`), take `PDO $db` in the constructor, and include standard CRUD methods (`read`, `readAll`, `write`, `edit`, `delete`).
