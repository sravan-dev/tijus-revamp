<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class S2M_Migrator {

    private $sqlite_path;
    private $pdo;
    private $errors   = array();
    private $warnings = array();
    private $stats    = array(
        'tables_created' => 0,
        'rows_inserted'  => 0,
        'tables_skipped' => 0,
    );

    // SQLite → MySQL type map
    private $type_map = array(
        'integer'          => 'bigint(20)',
        'int'              => 'int(11)',
        'tinyint'          => 'tinyint(4)',
        'smallint'         => 'smallint(6)',
        'mediumint'        => 'mediumint(9)',
        'bigint'           => 'bigint(20)',
        'real'             => 'double',
        'float'            => 'float',
        'double'           => 'double',
        'numeric'          => 'decimal(15,4)',
        'decimal'          => 'decimal(15,4)',
        'text'             => 'longtext',
        'blob'             => 'longblob',
        'none'             => 'longtext',
        'boolean'          => 'tinyint(1)',
        'bool'             => 'tinyint(1)',
        'date'             => 'date',
        'datetime'         => 'datetime',
        'timestamp'        => 'timestamp',
        'time'             => 'time',
        'year'             => 'year',
        'char'             => 'char(255)',
        'varchar'          => 'varchar(255)',
        'nchar'            => 'char(255)',
        'nvarchar'         => 'varchar(255)',
        'clob'             => 'longtext',
    );

    public function __construct( $sqlite_path ) {
        $this->sqlite_path = $sqlite_path;
    }

    /**
     * Open SQLite connection via PDO.
     */
    public function connect() {
        if ( ! class_exists( 'PDO' ) ) {
            throw new Exception( 'PDO extension not available.' );
        }
        $drivers = PDO::getAvailableDrivers();
        if ( ! in_array( 'sqlite', $drivers, true ) ) {
            throw new Exception( 'PDO SQLite driver not available on this server.' );
        }
        if ( ! file_exists( $this->sqlite_path ) ) {
            throw new Exception( 'SQLite file not found: ' . basename( $this->sqlite_path ) );
        }
        $this->pdo = new PDO( 'sqlite:' . $this->sqlite_path );
        $this->pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
    }

    /**
     * Return list of user tables in SQLite DB.
     */
    public function get_tables() {
        $stmt = $this->pdo->query(
            "SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name"
        );
        return $stmt->fetchAll( PDO::FETCH_COLUMN );
    }

    /**
     * Analyze: return tables + row counts.
     */
    public function analyze() {
        $tables = $this->get_tables();
        $result = array();
        foreach ( $tables as $table ) {
            $count = $this->pdo->query( "SELECT COUNT(*) FROM " . $this->quote_identifier( $table ) )->fetchColumn();
            $cols  = $this->get_columns( $table );
            $result[] = array(
                'table'    => $table,
                'rows'     => (int) $count,
                'columns'  => count( $cols ),
            );
        }
        return $result;
    }

    /**
     * Run full migration for selected tables.
     *
     * @param array  $tables        Table names to migrate (empty = all).
     * @param bool   $drop_existing Drop existing MySQL tables before create.
     * @param string $prefix        Prefix to prepend to table names.
     * @param int    $batch_size    Rows per INSERT batch.
     */
    public function migrate( $tables = array(), $drop_existing = false, $prefix = '', $batch_size = 500 ) {
        global $wpdb;

        $all_tables = $this->get_tables();
        if ( ! empty( $tables ) ) {
            $all_tables = array_intersect( $all_tables, $tables );
        }

        $wpdb->show_errors();

        foreach ( $all_tables as $table ) {
            $mysql_table = $prefix . $table;

            try {
                $create_sql = $this->build_create_table( $table, $mysql_table );

                if ( $drop_existing ) {
                    $wpdb->query( 'DROP TABLE IF EXISTS ' . $this->quote_identifier( $mysql_table ) );
                }

                // Check if table already exists
                $exists = $wpdb->get_var( $wpdb->prepare(
                    "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = %s AND table_name = %s",
                    DB_NAME, $mysql_table
                ) );

                if ( $exists && ! $drop_existing ) {
                    $this->warnings[] = "Table `{$mysql_table}` already exists — skipped. Enable 'Drop existing tables' to overwrite.";
                    $this->stats['tables_skipped']++;
                    continue;
                }

                // Create table
                $wpdb->query( $create_sql );
                if ( $wpdb->last_error ) {
                    throw new Exception( $wpdb->last_error );
                }
                $this->stats['tables_created']++;

                // Migrate indexes
                $this->migrate_indexes( $table, $mysql_table );

                // Migrate rows in batches
                $offset = 0;
                while ( true ) {
                    $rows = $this->pdo->query(
                        "SELECT * FROM " . $this->quote_identifier( $table ) .
                        " LIMIT {$batch_size} OFFSET {$offset}"
                    )->fetchAll( PDO::FETCH_ASSOC );

                    if ( empty( $rows ) ) break;

                    $this->batch_insert( $mysql_table, $rows );
                    $this->stats['rows_inserted'] += count( $rows );
                    $offset += $batch_size;
                }

            } catch ( Exception $e ) {
                $this->errors[] = "Table `{$table}`: " . $e->getMessage();
            }
        }

        return array(
            'stats'    => $this->stats,
            'errors'   => $this->errors,
            'warnings' => $this->warnings,
        );
    }

    // ─────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────

    private function get_columns( $table ) {
        $stmt = $this->pdo->query( "PRAGMA table_info(" . $this->quote_identifier( $table ) . ")" );
        return $stmt->fetchAll( PDO::FETCH_ASSOC );
    }

    private function build_create_table( $sqlite_table, $mysql_table ) {
        $cols    = $this->get_columns( $sqlite_table );
        $pk_cols = array();
        $parts   = array();

        foreach ( $cols as $col ) {
            $name     = $col['name'];
            $raw_type = strtolower( trim( $col['type'] ) );
            $mysql_type = $this->resolve_type( $raw_type );
            $notnull  = $col['notnull'] ? 'NOT NULL' : 'NULL';
            $default  = '';

            if ( $col['dflt_value'] !== null ) {
                $dv = $col['dflt_value'];
                // Strip surrounding quotes SQLite adds
                $dv = trim( $dv, "'" );
                if ( strtolower( $dv ) === 'null' ) {
                    $default = 'DEFAULT NULL';
                } elseif ( is_numeric( $dv ) ) {
                    $default = 'DEFAULT ' . $dv;
                } else {
                    $default = "DEFAULT '" . esc_sql( $dv ) . "'";
                }
            }

            if ( $col['pk'] ) {
                $pk_cols[] = $this->quote_identifier( $name );
            }

            // Auto-increment only on single integer PK
            $auto = '';
            if ( $col['pk'] && count( array_filter( $cols, fn($c) => $c['pk'] ) ) === 1
                 && strpos( $raw_type, 'int' ) !== false ) {
                $auto    = 'AUTO_INCREMENT';
                $notnull = 'NOT NULL';
                $default = '';
            }

            $parts[] = trim( implode( ' ', array_filter( array(
                $this->quote_identifier( $name ),
                $mysql_type,
                $notnull,
                $auto,
                $default,
            ) ) ) );
        }

        if ( count( $pk_cols ) === 1 && strpos( implode( '', array_map( 'strtolower', array_column( $cols, 'type' ) ) ), 'int' ) !== false ) {
            $parts[] = 'PRIMARY KEY (' . implode( ', ', $pk_cols ) . ')';
        } elseif ( ! empty( $pk_cols ) ) {
            $parts[] = 'PRIMARY KEY (' . implode( ', ', $pk_cols ) . ')';
        }

        $charset = defined( 'DB_CHARSET' ) ? DB_CHARSET : 'utf8mb4';
        $collate = defined( 'DB_COLLATE' ) && DB_COLLATE ? DB_COLLATE : 'utf8mb4_unicode_ci';

        return "CREATE TABLE " . $this->quote_identifier( $mysql_table ) . " (\n  "
            . implode( ",\n  ", $parts )
            . "\n) ENGINE=InnoDB DEFAULT CHARSET={$charset} COLLATE={$collate};";
    }

    private function migrate_indexes( $sqlite_table, $mysql_table ) {
        global $wpdb;
        $stmt = $this->pdo->query(
            "SELECT name, sql FROM sqlite_master WHERE type='index' AND tbl_name=" .
            $this->pdo->quote( $sqlite_table ) . " AND sql IS NOT NULL"
        );
        $indexes = $stmt->fetchAll( PDO::FETCH_ASSOC );
        foreach ( $indexes as $idx ) {
            // Parse: CREATE [UNIQUE] INDEX name ON table (cols)
            if ( preg_match( '/CREATE\s+(UNIQUE\s+)?INDEX\s+\S+\s+ON\s+\S+\s*\((.+)\)/i', $idx['sql'], $m ) ) {
                $unique   = $m[1] ? 'UNIQUE' : '';
                $cols_raw = $m[2];
                // Rebuild col list quoting each col
                $col_list = implode( ', ', array_map( function( $c ) {
                    $c = trim( $c, " `\"'" );
                    // Strip length specifiers from SQLite (not valid in some forms)
                    $c = preg_replace( '/\(\d+\)/', '', $c );
                    return $this->quote_identifier( $c );
                }, explode( ',', $cols_raw ) ) );

                $idx_name = 'idx_' . $mysql_table . '_' . md5( $idx['name'] );
                $sql      = "ALTER TABLE " . $this->quote_identifier( $mysql_table ) .
                            " ADD {$unique} INDEX " . $this->quote_identifier( $idx_name ) .
                            " ({$col_list})";
                $wpdb->query( $sql );
                // Ignore index errors silently (duplicate, etc.)
            }
        }
    }

    private function batch_insert( $mysql_table, $rows ) {
        global $wpdb;
        if ( empty( $rows ) ) return;

        $cols        = array_keys( $rows[0] );
        $col_list    = implode( ', ', array_map( array( $this, 'quote_identifier' ), $cols ) );
        $placeholders = '(' . implode( ', ', array_fill( 0, count( $cols ), '%s' ) ) . ')';
        $all_placeholders = implode( ', ', array_fill( 0, count( $rows ), $placeholders ) );

        $values = array();
        foreach ( $rows as $row ) {
            foreach ( $cols as $col ) {
                $values[] = $row[ $col ];
            }
        }

        $sql = "INSERT INTO " . $this->quote_identifier( $mysql_table ) .
               " ({$col_list}) VALUES {$all_placeholders}";

        // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        $wpdb->query( $wpdb->prepare( $sql, $values ) );

        if ( $wpdb->last_error ) {
            throw new Exception( $wpdb->last_error );
        }
    }

    private function resolve_type( $raw ) {
        // Strip length: varchar(255) → varchar
        $base = preg_replace( '/\(.+\)/', '', strtolower( trim( $raw ) ) );
        $base = trim( $base );

        // Preserve original length for varchar/char if present
        if ( preg_match( '/^(n?varchar|n?char)\s*\((\d+)\)/i', $raw, $m ) ) {
            $len = min( (int) $m[2], 65535 );
            $type = strtolower( $m[1] );
            return ( strpos( $type, 'char' ) !== false && strpos( $type, 'var' ) === false )
                ? "char({$len})"
                : "varchar({$len})";
        }
        if ( preg_match( '/^decimal\s*\((.+)\)/i', $raw, $m ) ) {
            return "decimal({$m[1]})";
        }

        return $this->type_map[ $base ] ?? 'longtext';
    }

    private function quote_identifier( $name ) {
        return '`' . str_replace( '`', '``', $name ) . '`';
    }
}
