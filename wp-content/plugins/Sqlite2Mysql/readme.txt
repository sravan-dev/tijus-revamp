=== Sqlite2Mysql ===
Contributors:      yourname
Tags:              sqlite, mysql, database, migration, import
Requires at least: 5.5
Tested up to:      6.5
Stable tag:        1.0.0
Requires PHP:      7.4
License:           GPL-2.0+
License URI:       https://www.gnu.org/licenses/gpl-2.0.html

Migrate SQLite databases to MySQL directly from your WordPress dashboard.

== Description ==

Sqlite2Mysql lets you upload a SQLite database file and migrate its tables and
data into your WordPress MySQL database — all from the admin panel.

**Features:**
* Drag & drop SQLite file upload
* Auto-detects tables, columns, row counts
* Per-table selection — pick exactly what to migrate
* SQLite → MySQL type conversion (VARCHAR, TEXT, BLOB, INTEGER, REAL, DECIMAL…)
* Index migration (UNIQUE + regular)
* Optional table prefix
* Drop-and-recreate or skip existing tables
* Configurable batch size for large databases
* Full migration history log
* Server requirements check on dashboard

**Requirements:**
* PHP 7.4+
* PDO extension with SQLite driver (`pdo_sqlite`)

== Installation ==

1. Upload the `sqlite2mysql` folder to `/wp-content/plugins/`.
2. Activate via *Plugins → Installed Plugins*.
3. Navigate to **Sqlite2Mysql** in the left sidebar.

== Screenshots ==

1. Dashboard with stats and requirements check.
2. New Migration — upload and table selection.
3. Migration result with stats and error report.
4. History page.

== Changelog ==

= 1.0.0 =
* Initial release.
