<?php

namespace OnlineVisitors;

use DateTime;

require_once('class.db.sqlite3.base.php');

class ClsDataBaseOnlineVisitors extends ClsDataBaseSQLite3Base
{
    private function onlineVisitorsCheckIPAdressReInsert(string $TempRemoteIPAddress): bool
    {
        $SearchTimeStamp = strtotime("-10 Minutes");
        $TempSQLite3Result = $this->OVSQLite3->query("SELECT RemoteIPAddress, ServerRequestTimeStamp FROM OnlineVisitors WHERE RemoteIPAddress = '" . $TempRemoteIPAddress . "'");

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

        if (isset($this->OVSQlite3Stmt)) {
            $this->OVSQlite3Stmt->reset();
            $this->OVSQlite3Stmt->clear();
        }

        if ($this->OVSQlite3Stmt = $this->OVSQLite3->prepare("SELECT RemoteIPAddress FROM OnlineVisitors WHERE RemoteIPAddress = :RemoteIPAddress AND State = :State AND City = :City AND ZipCode = :ZipCode")) {
            $TempBindResult = $this->OVSQlite3Stmt->bindValue(':RemoteIPAddress', $TempRemoteIPAddress, SQLITE3_TEXT);
            $TempBindResult = $this->OVSQlite3Stmt->bindValue(':State', $TempState, SQLITE3_TEXT);
            $TempBindResult = $this->OVSQlite3Stmt->bindValue(':City', $TempCity, SQLITE3_TEXT);
            $TempBindResult = $this->OVSQlite3Stmt->bindValue(':ZipCode', $TempZipCode, SQLITE3_TEXT);

            if ($TempBindResult) {
                if ($this->OVSQLite3Result = $this->OVSQlite3Stmt->execute()) {
                    while ($Row = $this->OVSQLite3Result->fetchArray(SQLITE3_ASSOC)) {
                        $TempBool = true;
                    }

                    $this->OVSQLite3Result->finalize();
                    unset($this->OVSQLite3Result);
                }
            }
        }
        return $TempBool;
    }

    private function onlineVisitorsUpdateRecord(string $TempRemoteIPAddress, string $TempState, string $TempCity, string $TempZipCode, string $TempServerRequestTimeStamp): bool
    {
        $TempBool = false;

        if (isset($this->OVSQlite3Stmt)) {
            $this->OVSQlite3Stmt->reset();
            $this->OVSQlite3Stmt->clear();
        }

        if ($this->OVSQlite3Stmt = $this->OVSQLite3->prepare("UPDATE OnlineVisitors SET ServerRequestTimeStamp = :ServerRequestTimeStamp WHERE RemoteIPAddress = :RemoteIPAddress AND State = :State AND City = :City AND ZipCode = :ZipCode")) {
            $TempBindResult = $this->OVSQlite3Stmt->bindValue(':RemoteIPAddress', $TempRemoteIPAddress, SQLITE3_TEXT);
            $TempBindResult = $this->OVSQlite3Stmt->bindValue(':State', $TempState, SQLITE3_TEXT);
            $TempBindResult = $this->OVSQlite3Stmt->bindValue(':City', $TempCity, SQLITE3_TEXT);
            $TempBindResult = $this->OVSQlite3Stmt->bindValue(':ZipCode', $TempZipCode, SQLITE3_TEXT);
            $TempBindResult = $this->OVSQlite3Stmt->bindValue(':ServerRequestTimeStamp', $TempServerRequestTimeStamp, SQLITE3_TEXT);

            if ($TempBindResult) {
                if ($this->OVSQLite3Result = $this->OVSQlite3Stmt->execute()) {
                    $TempBool = true;
                    $this->OVSQLite3Result->finalize();
                    unset($this->OVSQLite3Result);
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
                    $this->OVSQlite3Stmt = $this->OVSQLite3->prepare("
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
                    $TempBindResult = $this->OVSQlite3Stmt->bindParam(':RemoteIPAddress', $TempRemoteIPAddress, SQLITE3_TEXT);
                    $TempBindResult = $this->OVSQlite3Stmt->bindParam(':RemoteExternalIPAddress', $TempRemoteExternalIPAddress, SQLITE3_TEXT);
                    $TempBindResult = $this->OVSQlite3Stmt->bindParam(':Country', $TempCountry, SQLITE3_TEXT);
                    $TempBindResult = $this->OVSQlite3Stmt->bindParam(':State', $TempState, SQLITE3_TEXT);
                    $TempBindResult = $this->OVSQlite3Stmt->bindParam(':City', $TempCity, SQLITE3_TEXT);
                    $TempBindResult = $this->OVSQlite3Stmt->bindParam(':ZipCode', $TempZipCode, SQLITE3_TEXT);
                    $TempBindResult = $this->OVSQlite3Stmt->bindParam(':TimeZone', $TempTimeZone, SQLITE3_TEXT);
                    $TempBindResult = $this->OVSQlite3Stmt->bindParam(':Organization', $TempOrganization, SQLITE3_TEXT);
                    $TempBindResult = $this->OVSQlite3Stmt->bindParam(':ServerLocalIPAddress', $TempServerLocalIPAddress, SQLITE3_TEXT);
                    $TempBindResult = $this->OVSQlite3Stmt->bindParam(':ServerExternalIPAddress', $TempServerExternalIPAddress, SQLITE3_TEXT);
                    $TempBindResult = $this->OVSQlite3Stmt->bindParam(':ServerHTTPHostName', $TempServerHTTPHostName, SQLITE3_TEXT);
                    $TempBindResult = $this->OVSQlite3Stmt->bindParam(':HTTPReferer', $TempHTTPReferer, SQLITE3_TEXT);
                    $TempBindResult = $this->OVSQlite3Stmt->bindParam(':RequestURI', $TempRequestURI, SQLITE3_TEXT);
                    $TempBindResult = $this->OVSQlite3Stmt->bindParam(':ServerScriptName', $TempServerScriptName, SQLITE3_TEXT);
                    $TempBindResult = $this->OVSQlite3Stmt->bindParam(':ServerRequestTimeStamp', $TempServerRequestTimeStamp, SQLITE3_TEXT);
                    $TempBindResult = $this->OVSQlite3Stmt->bindParam(':HTTPUserAgent', $TempHTTPUserAgent, SQLITE3_TEXT);

                    if ($TempBindResult) {
                        if ($this->OVSQLite3Result = $this->OVSQlite3Stmt->execute()) {
                            $TempBool = true;

                            $this->OVSQLite3Result->finalize();
                            unset($this->OVSQLite3Result);
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

        if (isset($this->OVSQlite3Stmt)) {
            $this->OVSQlite3Stmt->reset();
            $this->OVSQlite3Stmt->clear();
        }

        if (
            $this->OVSQlite3Stmt = $this->OVSQLite3->prepare("SELECT RemoteExternalIPAddress, State, City, ZipCode, ServerRequestTimeStamp FROM OnlineVisitors ORDER BY datetime(ServerRequestTimeStamp) DESC LIMIT 20")
        ) {
            if ($this->OVSQLite3Result = $this->OVSQlite3Stmt->execute()) {
                while (($Row = $this->OVSQLite3Result->fetchArray(SQLITE3_ASSOC))) {
                    $CurrentTime = new DateTime();
                    $RowTime = date_create($Row['ServerRequestTimeStamp']);

                    $DiffDate = date_diff($CurrentTime, $RowTime);
                    $TempAry[] = $Row['City'] . ', ' . $Row['State'] . ' ' . $Row['ZipCode'] . ' (' . $DiffDate->{'days'} . (($DiffDate->{'days'} != 1) ? ' Days ' : ' Day ') . $DiffDate->{'h'} . (($DiffDate->{'h'} != 1) ? ' Hours ' : ' Hour ') . $DiffDate->{'i'} . (($DiffDate->{'i'} != 1) ? ' Minutes ago) ' : ' Minute ago) ') . ' ' . $Row['RemoteExternalIPAddress'];

                    unset($RowTime);
                    unset($CurrentTime);
                }
                $this->OVSQLite3Result->finalize();
                unset($this->OVSQLite3Result);
            }
        }

        return $TempAry;
    }
    /* ===================================================================================================================== */
}
