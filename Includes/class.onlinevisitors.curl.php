<?php

// 01APR2024
namespace KAPNET;

class ClsOnlineVisitorsCurl
{
    private object $KAPNETCurl;
    private array $KAPNETOnlineVisitorsGeoIP;

    public function __construct()
    {
        // create a new cURL resource
        $this->KAPNETCurl = curl_init();

        // set URL and other appropriate options
        curl_setopt($this->KAPNETCurl, CURLOPT_HEADER, 0);
        curl_setopt($this->KAPNETCurl, CURLOPT_RETURNTRANSFER, true);

        $this->KAPNETOnlineVisitorsGeoIP = array();
    }

    public function populateIPAddressGeo(string $TempRemoteIPAddress, string $TempServerLocalIPAddress, string $TempServerHTTPHostName, string $TempServerScriptName, string $TempServerRequestTimeStamp): void
    {
        $Response = null;
        exec("ping -n 1 google.com", $Output, $Response);

        // if old-local resolves to 127.0.0.1 and REMOTE_ADDR resolves to 127.0.0.1, then get external IP. Otherwise, use REMOTE_ADDR
        if ($TempRemoteIPAddress == $TempServerLocalIPAddress) {
            curl_setopt($this->KAPNETCurl, CURLOPT_URL, "http://ip-api.com/json/" . (($Response == 0) ? file_get_contents("http://ipecho.net/plain") : ""));
        } else {
            curl_setopt($this->KAPNETCurl, CURLOPT_URL, "http://ip-api.com/json/" . $TempRemoteIPAddress);
        }

        if ($Response == 0) {
            $this->KAPNETOnlineVisitorsGeoIP = json_decode(curl_exec($this->KAPNETCurl), true);
        }

        if (($TempRemoteIPAddress == null) || ($TempRemoteIPAddress == "")) {
            if (isset($this->KAPNETOnlineVisitorsGeoIP['query'])) {
                $this->KAPNETOnlineVisitorsGeoIP['RemoteIPAddress'] = $this->KAPNETOnlineVisitorsGeoIP['query'];
            } else {
                $this->KAPNETOnlineVisitorsGeoIP['RemoteIPAddress'] = "null or empty";
            }
        } else {
            $this->KAPNETOnlineVisitorsGeoIP['RemoteIPAddress'] = $TempRemoteIPAddress;
            $this->KAPNETOnlineVisitorsGeoIP['RemoteExternalIPAddress'] =  (($Response == 0) ? file_get_contents("http://ipecho.net/plain") : "No Internet");
        }

        $this->KAPNETOnlineVisitorsGeoIP['ServerLocalIPAddress'] = $TempServerLocalIPAddress;
        $this->KAPNETOnlineVisitorsGeoIP['ServerExternalIPAddress'] = gethostbyname($TempServerHTTPHostName);
        $this->KAPNETOnlineVisitorsGeoIP['ServerHTTPHostName'] = $TempServerHTTPHostName;
        $this->KAPNETOnlineVisitorsGeoIP['ServerScriptName'] = $TempServerScriptName;
        $this->KAPNETOnlineVisitorsGeoIP['ServerRequestTimeStamp'] = date("Y-m-d H:i:s", date($TempServerRequestTimeStamp));
    }

    /* ===================================================================================================================== */

    public function getRemoteIPAddress(): string
    {
        if (isset($this->KAPNETOnlineVisitorsGeoIP['RemoteIPAddress'])) {
            return $this->KAPNETOnlineVisitorsGeoIP['RemoteIPAddress'];
        } else {
            return "No Remote IP Adress";
        }
    }

    public function getRemoteExternalIPAddress(): string
    {
        if (isset($this->KAPNETOnlineVisitorsGeoIP['RemoteExternalIPAddress'])) {
            return $this->KAPNETOnlineVisitorsGeoIP['RemoteExternalIPAddress'];
        } else {
            return "No Remote External IP Adress";
        }
    }

    /* ===================================================================================================================== */

    public function getCountry(): string
    {
        if (isset($this->KAPNETOnlineVisitorsGeoIP['country'])) {
            return $this->KAPNETOnlineVisitorsGeoIP['country'];
        } else {
            return "No Country";
        }
    }

    public function getState(): string
    {
        if (isset($this->KAPNETOnlineVisitorsGeoIP['regionName'])) {
            return $this->KAPNETOnlineVisitorsGeoIP['regionName'];
        } else {
            return "No State";
        }
    }

    public function getCity(): string
    {
        if (isset($this->KAPNETOnlineVisitorsGeoIP['city'])) {
            return $this->KAPNETOnlineVisitorsGeoIP['city'];
        } else {
            return "No City";
        }
    }

    public function getZipCode(): string
    {
        if (isset($this->KAPNETOnlineVisitorsGeoIP['zip'])) {
            return $this->KAPNETOnlineVisitorsGeoIP['zip'];
        } else {
            return "No Zip Code";
        }
    }

    public function getTimeZone(): string
    {
        if (isset($this->KAPNETOnlineVisitorsGeoIP['timezone'])) {
            return $this->KAPNETOnlineVisitorsGeoIP['timezone'];
        } else {
            return "No Time Zone";
        }
    }

    public function getOrganization(): string
    {
        if (isset($this->KAPNETOnlineVisitorsGeoIP['isp'])) {
            return $this->KAPNETOnlineVisitorsGeoIP['isp'];
        } else {
            return "No Organization";
        }
    }

    /* ===================================================================================================================== */

    public function getServerLocalIPAddress(): string
    {
        if (isset($this->KAPNETOnlineVisitorsGeoIP['ServerLocalIPAddress'])) {
            return $this->KAPNETOnlineVisitorsGeoIP['ServerLocalIPAddress'];
        } else {
            return "No Server Local IP Adress";
        }
    }

    public function getServerExternalIPAddress(): string
    {
        if (isset($this->KAPNETOnlineVisitorsGeoIP['ServerExternalIPAddress'])) {
            return $this->KAPNETOnlineVisitorsGeoIP['ServerExternalIPAddress'];
        } else {
            return "No Server External IP Adress";
        }
    }

    public function getServerHTTPHostName(): string
    {
        if (isset($this->KAPNETOnlineVisitorsGeoIP['ServerHTTPHostName'])) {
            return $this->KAPNETOnlineVisitorsGeoIP['ServerHTTPHostName'];
        } else {
            return "No Server HTTP Host Name";
        }
    }

    public function getServerScriptName(): string
    {
        if (isset($this->KAPNETOnlineVisitorsGeoIP['ServerScriptName'])) {
            return $this->KAPNETOnlineVisitorsGeoIP['ServerScriptName'];
        } else {
            return "No Server Script Name";
        }
    }

    public function getServerRequestTimeStamp(): string
    {
        if (isset($this->KAPNETOnlineVisitorsGeoIP['ServerRequestTimeStamp'])) {
            return $this->KAPNETOnlineVisitorsGeoIP['ServerRequestTimeStamp'];
        } else {
            return "No Server Request Time Stamp";
        }
    }

    /* ===================================================================================================================== */

    public function __destruct()
    {
        // close cURL resource, and free up system resources
        unset($this->KAPNETOnlineVisitorsGeoIP);
        curl_close($this->KAPNETCurl);
        unset($this->KAPNETCurl);
    }
}
