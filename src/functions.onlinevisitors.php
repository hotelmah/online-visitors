<?php

declare(strict_types=1);

namespace OnlineVisitors;

date_default_timezone_set("America/Chicago");

require_once('class.db.sqlite3.onlinevisitors.php');
require_once('class.onlinevisitors.curl.php');

/* ===================================================================================================================== */

function executeOnlineVisitorsInsertRow(): int
{
    $TempInt = -1;

    if (empty($_SERVER['DOCUMENT_ROOT'])) {
        $DatabaseOnlineVisitors = new ClsDataBaseOnlineVisitors('', 'OnlineVisitors.db');
    } else {
        $DatabaseOnlineVisitors = new ClsDataBaseOnlineVisitors($_SERVER['DOCUMENT_ROOT'], 'OnlineVisitors.db');
    }

    $OnlineVisitorsCurl = new ClsOnlineVisitorsCurl();

    /* ===================================================================================================================== */

    if ($DatabaseOnlineVisitors->getDatabaseStatus() == "OnlineVisitorsReady") {
        if (empty($_SERVER['DOCUMENT_ROOT'])) {
            $OnlineVisitorsCurl->populateIPAddressGeo("127.0.0.1", "127.0.0.1", "local.example.com", "function.onlinevisitors.php", (string)time());
        } else {
            $OnlineVisitorsCurl->populateIPAddressGeo($_SERVER['REMOTE_ADDR'], ((isset($_SERVER['LOCAL_ADDR']) ? $_SERVER['LOCAL_ADDR'] : $_SERVER['SERVER_ADDR'])), $_SERVER['HTTP_HOST'], $_SERVER['SCRIPT_NAME'], (string)$_SERVER['REQUEST_TIME']);
        }

        $TempRemoteIPAddress = $OnlineVisitorsCurl->getRemoteIPAddress();
        $TempRemoteExternalIPAddress = $OnlineVisitorsCurl->getRemoteExternalIPAddress();

        $TempCountry = $OnlineVisitorsCurl->getCountry();
        $TempState =  $OnlineVisitorsCurl->getState();
        $TempCity = $OnlineVisitorsCurl->getCity();
        $TempZipCode = $OnlineVisitorsCurl->getZipCode();
        $TempTimeZone = $OnlineVisitorsCurl->getTimeZone();
        $TempOrganization = $OnlineVisitorsCurl->getOrganization();

        $TempServerLocalIPAddress = $OnlineVisitorsCurl->getServerLocalIPAddress();
        $TempServerExternalIPAddress = $OnlineVisitorsCurl->getServerExternalIPAddress();
        $TempServerHTTPHostName = $OnlineVisitorsCurl->getServerHTTPHostName();

        $TempHTTPReferer = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "Undefined");
        $TempRequestURI = (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : "Undefined");
        $TempServerScriptName = $OnlineVisitorsCurl->getServerScriptName();
        $TempServerRequestTimeStamp = $OnlineVisitorsCurl->getServerRequestTimeStamp();
        $TempHTTPUserAgent = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "Undefined");

        if ($DatabaseOnlineVisitors->onlineVisitorsInsertRow($TempRemoteIPAddress, $TempRemoteExternalIPAddress, $TempCountry, $TempState, $TempCity, $TempZipCode, $TempTimeZone, $TempOrganization, $TempServerLocalIPAddress, $TempServerExternalIPAddress, $TempServerHTTPHostName, $TempHTTPReferer, $TempRequestURI, $TempServerScriptName, $TempServerRequestTimeStamp, $TempHTTPUserAgent)) {
            $TempInt = $DatabaseOnlineVisitors->getLastInsertRowID();
        }

        unset($OnlineVisitorsCurl);
        unset($DatabaseOnlineVisitors);
    }
    return $TempInt;
}

/* ===================================================================================================================== */

function executeGetOnlineVisitorsLatestIPAddressesWithTime(): array
{
    $TempAry = array();

    if (empty($_SERVER['DOCUMENT_ROOT'])) {
        $DatabaseOnlineVisitors = new ClsDataBaseOnlineVisitors('', 'OnlineVisitors.db');
    } else {
        $DatabaseOnlineVisitors = new ClsDataBaseOnlineVisitors($_SERVER['DOCUMENT_ROOT'], 'OnlineVisitors.db');
    }

    $TempAry = $DatabaseOnlineVisitors->getOnlineVisitorsLatestIPAddressesWithTime();

    unset($DatabaseOnlineVisitors);

    return $TempAry;
}

/* ===================================================================================================================== */
