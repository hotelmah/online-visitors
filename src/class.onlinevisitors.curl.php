<?php

declare(strict_types=1);

// 01APR2024
namespace OnlineVisitors;

date_default_timezone_set("America/Chicago");

class ClsOnlineVisitorsCurl
{
    private object $OVCurl;
    private array $OnlineVisitorsGeoIP;

    public function __construct()
    {
        // create a new cURL resource
        $this->OVCurl = curl_init();

        // set URL and other appropriate options
        curl_setopt($this->OVCurl, CURLOPT_HEADER, 0);
        curl_setopt($this->OVCurl, CURLOPT_RETURNTRANSFER, true);

        $this->OnlineVisitorsGeoIP = array();
    }

    public function populateIPAddressGeo(string $TempRemoteIPAddress, string $TempServerLocalIPAddress, string $TempServerHTTPHostName, string $TempServerScriptName, string $TempServerRequestTimeStamp): void
    {
        $Response = null;

        // $Output = system("ping -c 1 google.com", $Response);
        if (isset($_SERVER['LOCAL_ADDR'])) {
            exec("ping -n 1 google.com", $Output, $Response);
        } elseif (isset($_SERVER['SERVER_ADDR'])) {
            exec("ping -c 1 google.com", $Output, $Response);
        }

        // if old-local resolves to 127.0.0.1 and REMOTE_ADDR resolves to 127.0.0.1, then get external IP. Otherwise, use REMOTE_ADDR
        if ($TempRemoteIPAddress == $TempServerLocalIPAddress) {
            curl_setopt($this->OVCurl, CURLOPT_URL, "http://ip-api.com/json/" . (($Response == 0) ? file_get_contents("http://ipecho.net/plain") : ""));
        } else {
            curl_setopt($this->OVCurl, CURLOPT_URL, "http://ip-api.com/json/" . $TempRemoteIPAddress);
        }

        if ($Response == 0) {
            $TempJsonDecode = json_decode(curl_exec($this->OVCurl), true);
            if (is_null($TempJsonDecode)) {
                $this->OnlineVisitorsGeoIP = [];
            }
            $this->OnlineVisitorsGeoIP = $TempJsonDecode;
        }

        if (($TempRemoteIPAddress == null) || ($TempRemoteIPAddress == "")) {
            if (isset($this->OnlineVisitorsGeoIP['query'])) {
                $this->OnlineVisitorsGeoIP['RemoteIPAddress'] = $this->OnlineVisitorsGeoIP['query'];
            } else {
                $this->OnlineVisitorsGeoIP['RemoteIPAddress'] = "null or empty";
            }
        } else {
            $this->OnlineVisitorsGeoIP['RemoteIPAddress'] = $TempRemoteIPAddress;
            $this->OnlineVisitorsGeoIP['RemoteExternalIPAddress'] =  (($Response == 0) ? file_get_contents("http://ipecho.net/plain") : "No Internet");
        }

        $this->OnlineVisitorsGeoIP['ServerLocalIPAddress'] = $TempServerLocalIPAddress;
        $this->OnlineVisitorsGeoIP['ServerExternalIPAddress'] = gethostbyname($TempServerHTTPHostName);
        $this->OnlineVisitorsGeoIP['ServerHTTPHostName'] = $TempServerHTTPHostName;
        $this->OnlineVisitorsGeoIP['ServerScriptName'] = $TempServerScriptName;
        $this->OnlineVisitorsGeoIP['ServerRequestTimeStamp'] = date("Y-m-d H:i:s", (int)date($TempServerRequestTimeStamp));
    }

    /* ===================================================================================================================== */

    public function getRemoteIPAddress(): string
    {
        if (isset($this->OnlineVisitorsGeoIP['RemoteIPAddress'])) {
            return $this->OnlineVisitorsGeoIP['RemoteIPAddress'];
        } else {
            return "No Remote IP Address";
        }
    }

    public function getRemoteExternalIPAddress(): string
    {
        if (isset($this->OnlineVisitorsGeoIP['RemoteExternalIPAddress'])) {
            return $this->OnlineVisitorsGeoIP['RemoteExternalIPAddress'];
        } else {
            return "No Remote External IP Address";
        }
    }

    /* ===================================================================================================================== */

    public function getCountry(): string
    {
        if (isset($this->OnlineVisitorsGeoIP['country'])) {
            return $this->OnlineVisitorsGeoIP['country'];
        } else {
            return "No Country";
        }
    }

    public function getState(): string
    {
        if (isset($this->OnlineVisitorsGeoIP['regionName'])) {
            return $this->OnlineVisitorsGeoIP['regionName'];
        } else {
            return "No State";
        }
    }

    public function getCity(): string
    {
        if (isset($this->OnlineVisitorsGeoIP['city'])) {
            return $this->OnlineVisitorsGeoIP['city'];
        } else {
            return "No City";
        }
    }

    public function getZipCode(): string
    {
        if (isset($this->OnlineVisitorsGeoIP['zip'])) {
            return $this->OnlineVisitorsGeoIP['zip'];
        } else {
            return "No Zip Code";
        }
    }

    public function getTimeZone(): string
    {
        if (isset($this->OnlineVisitorsGeoIP['timezone'])) {
            return $this->OnlineVisitorsGeoIP['timezone'];
        } else {
            return "No Time Zone";
        }
    }

    public function getOrganization(): string
    {
        if (isset($this->OnlineVisitorsGeoIP['isp'])) {
            return $this->OnlineVisitorsGeoIP['isp'];
        } else {
            return "No Organization";
        }
    }

    /* ===================================================================================================================== */

    public function getServerLocalIPAddress(): string
    {
        if (isset($this->OnlineVisitorsGeoIP['ServerLocalIPAddress'])) {
            return $this->OnlineVisitorsGeoIP['ServerLocalIPAddress'];
        } else {
            return "No Server Local IP Address";
        }
    }

    public function getServerExternalIPAddress(): string
    {
        if (isset($this->OnlineVisitorsGeoIP['ServerExternalIPAddress'])) {
            return $this->OnlineVisitorsGeoIP['ServerExternalIPAddress'];
        } else {
            return "No Server External IP Address";
        }
    }

    public function getServerHTTPHostName(): string
    {
        if (isset($this->OnlineVisitorsGeoIP['ServerHTTPHostName'])) {
            return $this->OnlineVisitorsGeoIP['ServerHTTPHostName'];
        } else {
            return "No Server HTTP Host Name";
        }
    }

    public function getServerScriptName(): string
    {
        if (isset($this->OnlineVisitorsGeoIP['ServerScriptName'])) {
            return $this->OnlineVisitorsGeoIP['ServerScriptName'];
        } else {
            return "No Server Script Name";
        }
    }

    public function getServerRequestTimeStamp(): string
    {
        if (isset($this->OnlineVisitorsGeoIP['ServerRequestTimeStamp'])) {
            return $this->OnlineVisitorsGeoIP['ServerRequestTimeStamp'];
        } else {
            return "No Server Request Time Stamp";
        }
    }

    /* ===================================================================================================================== */

    public function __destruct()
    {
        // close cURL resource, and free up system resources
        unset($this->OnlineVisitorsGeoIP);
        curl_close($this->OVCurl);
        unset($this->OVCurl);
    }
}
