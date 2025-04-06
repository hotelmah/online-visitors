<?php

declare(strict_types=1);

namespace OnlineVisitors;

date_default_timezone_set("America/Chicago");

use SQLite3;

class ClsDataBaseSQLite3Base
{
    protected object $OVSQLite3;
    protected object $OVSQlite3Stmt;
    protected object $OVSQLite3Result;
    protected string $OVServerDocumentRoot;
    protected string $OVDatabaseFileName;
    protected string $OVDatabasePurpose;
    protected string $OVDatabaseStatus;

    public function __construct($TempServerDocumentRoot = "", $TempDatabaseFileName = "Blank.db")
    {
        $this->OVServerDocumentRoot = $TempServerDocumentRoot;
        $this->OVDatabaseFileName = $TempDatabaseFileName;

        $this->databasePurposePopulate();

        if ($this->databaseCreateOpen()) {
            if ($this->createTable($this->OVDatabasePurpose)) {
                $this->OVDatabaseStatus = $this->OVDatabasePurpose . "Ready";
            } else {
                $this->OVDatabaseStatus = $this->OVDatabasePurpose . "NOT-Ready";
            }
        }
    }

    /* ===================================================================================================================== */

    private function databasePurposePopulate(): void
    {
        if (str_contains($this->OVDatabaseFileName, '.')) {
            $this->OVDatabasePurpose = current(explode('.', $this->OVDatabaseFileName));
        } else {
            $this->OVDatabasePurpose = "NoDatabasePurpose";
        }
    }

    private function databaseCreateOpen(): bool
    {
        if (empty($this->OVServerDocumentRoot)) {
            $this->OVSQLite3 = new SQLite3($this->OVDatabaseFileName);
        } else {
            $this->OVSQLite3 = new SQLite3($this->OVServerDocumentRoot . DIRECTORY_SEPARATOR . $this->OVDatabaseFileName);
        }
        $this->OVSQLite3->exec('PRAGMA journal_mode = wal;');
        $this->OVSQLite3->exec('PRAGMA synchronous = NORMAL;');
        // $this->OVSQLite3->exec('PRAGMA schema.taille_cache = 16000;');

        if ($this->OVSQLite3->busyTimeout(5000)) {
            if ($this->getLastErrorCode() == 0) {
                if ($this->getLastErrorMessage() == "not an error") {
                    return true;
                }
            }
        }
        return false;
    }

    /* ===================================================================================================================== */

    private function createTable($TempTableName): bool
    {
        $TempQuery = "CREATE TABLE IF NOT EXISTS " . $TempTableName . " (
            Counter INTEGER PRIMARY KEY,
            RemoteIPAddress TEXT NOT NULL,
            RemoteExternalIPAddress TEXT,
            Country TEXT,
            State TEXT,
            City TEXT,
            ZipCode TEXT,
            TimeZone TEXT,
            Organization TEXT,
            ServerLocalIPAddress TEXT,
            ServerExternalIPAddress TEXT,
            ServerHTTPHostName TEXT,
            HTTPReferer TEXT,
            RequestURI TEXT,
            ServerScriptName TEXT,
            ServerRequestTimeStamp TEXT NOT NULL,
            HTTPUserAgent TEXT)";

        if ($this->OVSQLite3->exec($TempQuery)) {
            if ($this->getLastErrorCode() == 0) {
                if ($this->getLastErrorMessage() == "not an error") {
                    $this->OVSQLite3->enableExceptions(true);
                    return true;
                }
            }
        }
        return false;
    }

    /* ===================================================================================================================== */

    public function getTableAllRecords(string $TempQueryType): array
    {
        $TempAllRecordsAry = array();
        $TempQuery = "";

        if (!empty($TempQueryType)) {
            $TempQuery = "SELECT * FROM " . $TempQueryType . " ORDER BY Counter DESC LIMIT 700";
        }

        $TempSQLite3Result = $this->OVSQLite3->query($TempQuery);
        if ($TempSQLite3Result->numColumns() > 0) {
            while ($Row = $TempSQLite3Result->fetchArray(SQLITE3_ASSOC)) {
                $TempAllRecordsAry[] = $Row;
            }
            $TempSQLite3Result->finalize();
            unset($TempSQLite3Result);
        }
        return $TempAllRecordsAry;
    }

    /* ===================================================================================================================== */

    public function getAllColumnNames(string $TempTableName): array
    {
        $TempColumnNamesAry = array();
        // $TempSQLite3Result = $this->OVSQLite3->query("SELECT name FROM pragma_table_info('" . $TempTableName . "') as tblInfo");
        $TempSQLite3Result = $this->OVSQLite3->query("PRAGMA table_info('" . $TempTableName . "');");
        if ($TempSQLite3Result->numColumns() > 0) {
            while ($Row = $TempSQLite3Result->fetchArray(SQLITE3_ASSOC)) {
                $TempColumnNamesAry[] = $Row['name'];
            }
            $TempSQLite3Result->finalize();
            unset($TempSQLite3Result);
        }
        return $TempColumnNamesAry;
    }

    public function getAllRecordsCount(string $TempTableName): int
    {
        $TempResult = $this->OVSQLite3->querySingle("SELECT COUNT(*) FROM " . $TempTableName);
        if (is_int($TempResult)) {
            return $TempResult;
        } else {
            return -1;
        }
    }

    /* ===================================================================================================================== */

    public function getDatabasePurpose(): string
    {
        return $this->OVDatabasePurpose;
    }

    public function getDatabaseStatus(): string
    {
        return $this->OVDatabaseStatus;
    }

    public function getLastInsertRowID(): int
    {
        return $this->OVSQLite3->lastInsertRowID();
    }

    public function getLastErrorCode(): int
    {
        return $this->OVSQLite3->lastErrorCode();
    }

    public function getLastErrorMessage(): string
    {
        return $this->OVSQLite3->lastErrorMsg();
    }

    public function getVersion(): string
    {
        $TempAry = array();
        $TempAry = $this->OVSQLite3->version();
        return "Version: " . $TempAry['versionString'];
    }

    /* ===================================================================================================================== */

    public function __destruct()
    {
        if (isset($this->OVSQlite3Stmt)) {
            $this->OVSQlite3Stmt->close();
            unset($this->OVSQlite3Stmt);
        }
        $this->OVSQLite3->close();
        unset($this->OVSQLite3);
    }
}
