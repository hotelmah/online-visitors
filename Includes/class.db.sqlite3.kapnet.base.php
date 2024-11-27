<?php

namespace KAPNET;

use SQLite3;

class ClsDataBaseSQLite3Base
{
    protected object $KAPNETSQLite3;
    protected object $KAPNETSQlite3Stmt;
    protected object $KAPNETSQLite3Result;
    protected string $KAPNETServerDocumentRoot;
    protected string $KAPNETDatabaseFileName;
    protected string $KAPNETDatabasePurpose;
    protected string $KAPNETDatabaseStatus;

    public function __construct($TempServerDocumentRoot = "", $TempDatabaseFileName = "Blank.db")
    {
        $this->KAPNETServerDocumentRoot = $TempServerDocumentRoot;
        $this->KAPNETDatabaseFileName = $TempDatabaseFileName;

        $this->databasePurposePopulate();

        if ($this->databaseCreateOpen()) {
            if ($this->createTable($this->KAPNETDatabasePurpose)) {
                $this->KAPNETDatabaseStatus = $this->KAPNETDatabasePurpose . "Ready";
            } else {
                $this->KAPNETDatabaseStatus = $this->KAPNETDatabasePurpose . "NOT-Ready";
            }
        }
    }

    /* ===================================================================================================================== */

    private function databasePurposePopulate(): void
    {
        if (str_contains($this->KAPNETDatabaseFileName, '.')) {
            $this->KAPNETDatabasePurpose = current(explode('.', $this->KAPNETDatabaseFileName));
        } else {
            $this->KAPNETDatabasePurpose = "NoDatabasePurpose";
        }
    }

    private function databaseCreateOpen(): bool
    {
        if (empty($this->KAPNETServerDocumentRoot)) {
            $this->KAPNETSQLite3 = new SQLite3($this->KAPNETDatabaseFileName);
        } else {
            $this->KAPNETSQLite3 = new SQLite3($this->KAPNETServerDocumentRoot . DIRECTORY_SEPARATOR . $this->KAPNETDatabaseFileName);
        }
        $this->KAPNETSQLite3->exec('PRAGMA journal_mode = wal;');
        $this->KAPNETSQLite3->exec('PRAGMA synchronous = NORMAL;');
        // $this->KAPNETSQLite3->exec('PRAGMA schema.taille_cache = 16000;');

        if ($this->KAPNETSQLite3->busyTimeout(5000)) {
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

        if ($this->KAPNETSQLite3->exec($TempQuery)) {
            if ($this->getLastErrorCode() == 0) {
                if ($this->getLastErrorMessage() == "not an error") {
                    $this->KAPNETSQLite3->enableExceptions(true);
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

        $TempSQLite3Result = $this->KAPNETSQLite3->query($TempQuery);
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
        $TempSQLite3Result = $this->KAPNETSQLite3->query("SELECT name FROM pragma_table_info('" . $TempTableName . "') as tblInfo");
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
        $TempResult = $this->KAPNETSQLite3->querySingle("SELECT COUNT(*) FROM " . $TempTableName);
        if (is_int($TempResult)) {
            return $TempResult;
        } else {
            return -1;
        }
    }

    /* ===================================================================================================================== */

    public function getDatabasePurpose(): string
    {
        return $this->KAPNETDatabasePurpose;
    }

    public function getDatabaseStatus(): string
    {
        return $this->KAPNETDatabaseStatus;
    }

    public function getLastInsertRowID(): int
    {
        return $this->KAPNETSQLite3->lastInsertRowID();
    }

    public function getLastErrorCode(): int
    {
        return $this->KAPNETSQLite3->lastErrorCode();
    }

    public function getLastErrorMessage(): string
    {
        return $this->KAPNETSQLite3->lastErrorMsg();
    }

    public function getVersion(): string
    {
        $TempAry = array();
        $TempAry = $this->KAPNETSQLite3->version();
        return "Version: " . $TempAry['versionString'];
    }

    /* ===================================================================================================================== */

    public function __destruct()
    {
        if (isset($this->KAPNETSQlite3Stmt)) {
            $this->KAPNETSQlite3Stmt->close();
            unset($this->KAPNETSQlite3Stmt);
        }
        $this->KAPNETSQLite3->close();
        unset($this->KAPNETSQLite3);
    }
}
