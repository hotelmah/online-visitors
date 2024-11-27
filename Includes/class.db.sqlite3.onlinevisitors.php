<?php

namespace KAPNET;

use DateTime;

require_once('class.db.sqlite3.kapnet.base.php');

class ClsDataBaseOnlineVisitors extends ClsDataBaseSQLite3Base
{
    private function onlineVisitorsCheckIPAdressReInsert(string $TempRemoteIPAddress): bool
    {
        $SearchTimeStamp = strtotime("-10 Minutes");
        $TempSQLite3Result = $this->KAPNETSQLite3->query("SELECT RemoteIPAddress, ServerRequestTimeStamp FROM OnlineVisitors WHERE RemoteIPAddress = '" . $TempRemoteIPAddress . "'");
        if ($TempSQLite3Result->numColumns() > 0) {
            $NumRows = 0;
            while ($Row = $TempSQLite3Result->fetchArray(SQLITE3_ASSOC)) {
                if (strtotime($Row['ServerRequestTimeStamp']) > $SearchTimeStamp) {
                    $NumRows++;
                }
            }

            $TempSQLite3Result->finalize();
            unset($TempSQLite3Result);

            if ($NumRows > 0) {
                return false;
            }
        }
        return true;
    }

    private function onlineVisitorsIsRecordInDatabase(string $TempRemoteIPAddress, string $TempState, string $TempCity, string $TempZipCode): bool
    {
        $TempBool = false;

        if (isset($this->KAPNETSQlite3Stmt)) {
            $this->KAPNETSQlite3Stmt->reset();
            $this->KAPNETSQlite3Stmt->clear();
        }

        if ($this->KAPNETSQlite3Stmt = $this->KAPNETSQLite3->prepare("SELECT RemoteIPAddress FROM OnlineVisitors WHERE RemoteIPAddress = :RemoteIPAddress AND State = :State AND City = :City AND ZipCode = :ZipCode")) {
            $TempBindResult = $this->KAPNETSQlite3Stmt->bindValue(':RemoteIPAddress', $TempRemoteIPAddress, SQLITE3_TEXT);
            $TempBindResult = $this->KAPNETSQlite3Stmt->bindValue(':State', $TempState, SQLITE3_TEXT);
            $TempBindResult = $this->KAPNETSQlite3Stmt->bindValue(':City', $TempCity, SQLITE3_TEXT);
            $TempBindResult = $this->KAPNETSQlite3Stmt->bindValue(':ZipCode', $TempZipCode, SQLITE3_TEXT);
            if ($TempBindResult) {
                if ($this->KAPNETSQLite3Result = $this->KAPNETSQlite3Stmt->execute()) {
                    while ($Row = $this->KAPNETSQLite3Result->fetchArray(SQLITE3_ASSOC)) {
                        $TempBool = true;
                    }

                    $this->KAPNETSQLite3Result->finalize();
                    unset($this->KAPNETSQLite3Result);
                }
            }
        }
        return $TempBool;
    }

    private function onlineVisitorsUpdateRecord(string $TempRemoteIPAddress, string $TempState, string $TempCity, string $TempZipCode, string $TempServerRequestTimeStamp): bool
    {
        $TempBool = false;

        if (isset($this->KAPNETSQlite3Stmt)) {
            $this->KAPNETSQlite3Stmt->reset();
            $this->KAPNETSQlite3Stmt->clear();
        }

        if ($this->KAPNETSQlite3Stmt = $this->KAPNETSQLite3->prepare("UPDATE OnlineVisitors SET ServerRequestTimeStamp = :ServerRequestTimeStamp WHERE RemoteIPAddress = :RemoteIPAddress AND State = :State AND City = :City AND ZipCode = :ZipCode")) {
            $TempBindResult = $this->KAPNETSQlite3Stmt->bindValue(':RemoteIPAddress', $TempRemoteIPAddress, SQLITE3_TEXT);
            $TempBindResult = $this->KAPNETSQlite3Stmt->bindValue(':State', $TempState, SQLITE3_TEXT);
            $TempBindResult = $this->KAPNETSQlite3Stmt->bindValue(':City', $TempCity, SQLITE3_TEXT);
            $TempBindResult = $this->KAPNETSQlite3Stmt->bindValue(':ZipCode', $TempZipCode, SQLITE3_TEXT);
            $TempBindResult = $this->KAPNETSQlite3Stmt->bindValue(':ServerRequestTimeStamp', $TempServerRequestTimeStamp, SQLITE3_TEXT);
            if ($TempBindResult) {
                if ($this->KAPNETSQLite3Result = $this->KAPNETSQlite3Stmt->execute()) {
                    $TempBool = true;
                    $this->KAPNETSQLite3Result->finalize();
                    unset($this->KAPNETSQLite3Result);
                }
            }
        }
        return $TempBool;
    }

    /* ===================================================================================================================== */

    public function onlineVisitorsInsertRow(string $TempRemoteIPAddress, string $TempRemoteExternalIPAddress, string $TempCountry, string $TempState, string $TempCity, string $TempZipCode, string $TempTimeZone, string $TempOrganization, string $TempServerLocalIPAddress, string $TempServerExternalIPAddress, string $TempServerHTTPHostName, string $TempHTTPReferer, string $TempRequestURI, string $TempServerScriptName, string $TempServerRequestTimeStamp, string $TempHTTPUserAgent): bool
    {
        $TempBool = false;
        if ($this->onlineVisitorsCheckIPAdressReInsert($TempRemoteIPAddress)) {
            if ($this->onlineVisitorsIsRecordInDatabase($TempRemoteIPAddress, $TempState, $TempCity, $TempZipCode)) {
                if ($this->onlineVisitorsUpdateRecord($TempRemoteIPAddress, $TempState, $TempCity, $TempZipCode, $TempServerRequestTimeStamp)) {
                    return true;
                }
            } else {
                if (
                    $this->KAPNETSQlite3Stmt = $this->KAPNETSQLite3->prepare("
                                INSERT INTO OnlineVisitors (
                                    RemoteIPAddress,
                                    RemoteExternalIPAddress,
                                    Country,
                                    State,
                                    City,
                                    ZipCode,
                                    TimeZone,
                                    Organization,
                                    ServerLocalIPAddress,
                                    ServerExternalIPAddress,
                                    ServerHTTPHostName,
                                    HTTPReferer,
                                    RequestURI,
                                    ServerScriptName,
                                    ServerRequestTimeStamp,
                                    HTTPUserAgent) VALUES (
                                    :RemoteIPAddress,
                                    :RemoteExternalIPAddress,
                                    :Country,
                                    :State,
                                    :City,
                                    :ZipCode,
                                    :TimeZone,
                                    :Organization,
                                    :ServerLocalIPAddress,
                                    :ServerExternalIPAddress,
                                    :ServerHTTPHostName,
                                    :HTTPReferer,
                                    :RequestURI,
                                    :ServerScriptName,
                                    :ServerRequestTimeStamp,
                                    :HTTPUserAgent)")
                ) {
                    $TempBindResult = $this->KAPNETSQlite3Stmt->bindParam(':RemoteIPAddress', $TempRemoteIPAddress, SQLITE3_TEXT);
                    $TempBindResult = $this->KAPNETSQlite3Stmt->bindParam(':RemoteExternalIPAddress', $TempRemoteExternalIPAddress, SQLITE3_TEXT);
                    $TempBindResult = $this->KAPNETSQlite3Stmt->bindParam(':Country', $TempCountry, SQLITE3_TEXT);
                    $TempBindResult = $this->KAPNETSQlite3Stmt->bindParam(':State', $TempState, SQLITE3_TEXT);
                    $TempBindResult = $this->KAPNETSQlite3Stmt->bindParam(':City', $TempCity, SQLITE3_TEXT);
                    $TempBindResult = $this->KAPNETSQlite3Stmt->bindParam(':ZipCode', $TempZipCode, SQLITE3_TEXT);
                    $TempBindResult = $this->KAPNETSQlite3Stmt->bindParam(':TimeZone', $TempTimeZone, SQLITE3_TEXT);
                    $TempBindResult = $this->KAPNETSQlite3Stmt->bindParam(':Organization', $TempOrganization, SQLITE3_TEXT);
                    $TempBindResult = $this->KAPNETSQlite3Stmt->bindParam(':ServerLocalIPAddress', $TempServerLocalIPAddress, SQLITE3_TEXT);
                    $TempBindResult = $this->KAPNETSQlite3Stmt->bindParam(':ServerExternalIPAddress', $TempServerExternalIPAddress, SQLITE3_TEXT);
                    $TempBindResult = $this->KAPNETSQlite3Stmt->bindParam(':ServerHTTPHostName', $TempServerHTTPHostName, SQLITE3_TEXT);
                    $TempBindResult = $this->KAPNETSQlite3Stmt->bindParam(':HTTPReferer', $TempHTTPReferer, SQLITE3_TEXT);
                    $TempBindResult = $this->KAPNETSQlite3Stmt->bindParam(':RequestURI', $TempRequestURI, SQLITE3_TEXT);
                    $TempBindResult = $this->KAPNETSQlite3Stmt->bindParam(':ServerScriptName', $TempServerScriptName, SQLITE3_TEXT);
                    $TempBindResult = $this->KAPNETSQlite3Stmt->bindParam(':ServerRequestTimeStamp', $TempServerRequestTimeStamp, SQLITE3_TEXT);
                    $TempBindResult = $this->KAPNETSQlite3Stmt->bindParam(':HTTPUserAgent', $TempHTTPUserAgent, SQLITE3_TEXT);
                    if ($TempBindResult) {
                        if ($this->KAPNETSQLite3Result = $this->KAPNETSQlite3Stmt->execute()) {
                            $TempBool = true;

                            $this->KAPNETSQLite3Result->finalize();
                            unset($this->KAPNETSQLite3Result);
                        }
                    }
                }
            }
        }
        return $TempBool;
    }

    /* ===================================================================================================================== */

    public function getOnlineVisitorsLatestIPAddressesWithTime(): array
    {
        $TempAry = array();

        if (isset($this->KAPNETSQlite3Stmt)) {
            $this->KAPNETSQlite3Stmt->reset();
            $this->KAPNETSQlite3Stmt->clear();
        }

        if (
            $this->KAPNETSQlite3Stmt = $this->KAPNETSQLite3->prepare("SELECT RemoteIPAddress, State, City, ZipCode, ServerRequestTimeStamp FROM OnlineVisitors ORDER BY datetime(ServerRequestTimeStamp) DESC LIMIT 20")
        ) {
            if ($this->KAPNETSQLite3Result = $this->KAPNETSQlite3Stmt->execute()) {
                while (($Row = $this->KAPNETSQLite3Result->fetchArray(SQLITE3_ASSOC))) {
                    $CurrentTime = new DateTime();
                    $RowTime = date_create($Row['ServerRequestTimeStamp']);

                    $DiffDate = date_diff($CurrentTime, $RowTime);
                    $TempAry[] = $Row['City'] . ', ' . $Row['State'] . ' ' . $Row['ZipCode'] . ' (' . $DiffDate->{'days'} . (($DiffDate->{'days'} != 1) ? ' Days ' : ' Day ') . $DiffDate->{'h'} . (($DiffDate->{'h'} != 1) ? ' Hours ' : ' Hour ') . $DiffDate->{'i'} . (($DiffDate->{'i'} != 1) ? ' Minutes ago) ' : ' Minute ago) ') . ' ' . $Row['RemoteIPAddress'];

                    unset($RowTime);
                    unset($CurrentTime);
                }
                $this->KAPNETSQLite3Result->finalize();
                unset($this->KAPNETSQLite3Result);
            }
        }

        return $TempAry;
    }
    /* ===================================================================================================================== */
}
