<?php

namespace Maxmind\Bundle\GeoipBundle\Service;

use Maxmind\lib\GeoIp;
use Maxmind\lib\GeoIpRegionVars;

/**
 * Class GeoipManager
 */
class GeoipManager
{
    protected $geoip = null;

    protected $record = null;

    /** @type string */
    private $licenseKey;

    /**
     * @param string $filePath
     * @param string $licenseKey
     */
    public function __construct($filePath, $licenseKey)
    {
        $this->geoip = new GeoIp($filePath);
        $this->licenseKey = $licenseKey;
    }

    /**
     * @param null $ip
     *
     * @return null
     */
    public function getCountryCode($ip = null)
    {
        if ($ip) {
            $this->lookup($ip);
        }

        if ($this->record) {
            return $this->record->country_code;
        }

        return $this->record;
    }

    /**
     * @param string $ip
     *
     * @return $this|bool
     */
    public function lookup($ip)
    {
        $this->record = $this->geoip->geoip_record_by_addr($ip);

        if ($this->record) {
            return $this;
        }

        return false;
    }

    /**
     * @param null $ip
     *
     * @return null
     */
    public function getCountryCode3($ip = null)
    {
        if ($ip) {
            $this->lookup($ip);
        }

        if ($this->record) {
            return $this->record->country_code3;
        }

        return $this->record;
    }

    /**
     * @param null $ip
     *
     * @return null
     */
    public function getCountryName($ip = null)
    {
        if ($ip) {
            $this->lookup($ip);
        }

        if ($this->record) {
            return $this->record->country_name;
        }

        return $this->record;
    }

    /**
     * @param null $ip
     *
     * @return null
     */
    public function getRegionCode($ip = null)
    {
        if ($ip) {
            $this->lookup($ip);
        }

        if ($this->record) {
            return $this->record->region;
        }

        return $this->record;
    }

    /**
     * @param null $ip
     *
     * @return null
     */
    public function getRegion($ip = null)
    {
        if ($ip) {
            $this->lookup($ip);
        }

        if ($this->record
            && $this->record->country_code
            && $this->record->region
        ) {
            return GeoIpRegionVars::$GEOIP_REGION_NAME[$this->record->country_code][$this->record->region];
        }

        return null;
    }

    /**
     * @param null $ip
     *
     * @return null
     */
    public function getCity($ip = null)
    {
        if ($ip) {
            $this->lookup($ip);
        }

        if ($this->record) {
            return $this->record->city;
        }

        return $this->record;
    }

    /**
     * @param null $ip
     *
     * @return null
     */
    public function getPostalCode($ip = null)
    {
        if ($ip) {
            $this->lookup($ip);
        }

        if ($this->record) {
            return $this->record->postal_code;
        }

        return $this->record;
    }

    /**
     * @param null $ip
     *
     * @return null
     */
    public function getLatitude($ip = null)
    {
        if ($ip) {
            $this->lookup($ip);
        }

        if ($this->record) {
            return $this->record->latitude;
        }

        return $this->record;
    }

    /**
     * @param null $ip
     *
     * @return null
     */
    public function getLongitude($ip = null)
    {
        if ($ip) {
            $this->lookup($ip);
        }

        if ($this->record) {
            return $this->record->longitude;
        }

        return $this->record;
    }

    /**
     * @param null $ip
     *
     * @return null
     */
    public function getAreaCode($ip = null)
    {
        if ($ip) {
            $this->lookup($ip);
        }

        if ($this->record) {
            return $this->record->area_code;
        }

        return $this->record;
    }

    /**
     * @param null $ip
     *
     * @return null
     */
    public function getMetroCode($ip = null)
    {
        if ($ip) {
            $this->lookup($ip);
        }

        if ($this->record) {
            return $this->record->metro_code;
        }

        return $this->record;
    }

    /**
     * @param null $ip
     *
     * @return null
     */
    public function getContinentCode($ip = null)
    {
        if ($ip) {
            $this->lookup($ip);
        }

        if ($this->record) {
            return $this->record->continent_code;
        }

        return $this->record;
    }

    /**
     * @param string $ip
     *
     * @return mixed
     * @throws \Exception
     */
    public function getScoreProxy($ip)
    {
        $query = "https://minfraud.maxmind.com/app/ipauth_http?l=".$this->licenseKey."&i=".$ip;
        $score = explode("=", file_get_contents($query));

        if ($score[1] == "LICENSE_REQUIRED") {
            throw new \Exception("MaxMind MinFraud Proxy license required");
        }

        if ($score[1] == "MAX_REQUESTS_REACHED") {
            throw new \Exception("MaxMind MinFraud Proxy invalid license or max request reached");
        }

        if (empty($score[1])) {
           throw new \Exception("MaxMind MinFraud Proxy invalid ip address");
        }

        return floatval($score[1]);
    }
}
