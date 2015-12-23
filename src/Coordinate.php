<?php
/**
 * This file is part of google-directions-client.
 *
 * (c) David Weichert <info@davidweichert.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Consoserv\GoogleDirections;


class Coordinate
{
    const GOOGLE_MIN_LAT = -85;

    const GOOGLE_MAX_LAT = 85;

    const GOOGLE_MIN_LONG = -180;

    const GOOGLE_MAX_LONG = 180;

    /**
     * Coordinate constructor.
     *
     * Coordinate defaults to Google Maps coordinates of Royal Observatory in Greenwich.
     *
     * @param double $latitude OPTIONAL
     * @param double $longitude OPTIONAL
     */
    public function __construct($latitude = 51.476780 , $longitude = 0.000479)
    {
        $this->setLatitude($latitude);
        $this->setLongitude($longitude);
    }

    /**
     * @var double
     */
    private $latitude;

    /**
     * @var double
     */
    private $longitude;

    /**
     * @return double
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param double $latitude
     * @return Coordinate
     */
    public function setLatitude($latitude)
    {
        $this->isValidLatitude($latitude);
        $this->latitude = (double)$latitude;
        return $this;
    }

    /**
     * @return double
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param double $longitude
     * @return Coordinate
     */
    public function setLongitude($longitude)
    {
        $this->isValidLongitude($longitude);
        $this->longitude = (double)$longitude;
        return $this;
    }

    private function isValidLatitude($value)
    {
        $errormsg = '';
        if ($value < self::GOOGLE_MIN_LAT)
        {
            $errormsg .= sprintf(
                'Latitude %s lesser than allowed (%s). ',
                $value,
                self::GOOGLE_MIN_LAT
            );
        }
        if ($value > self::GOOGLE_MAX_LAT)
        {
            $errormsg .= sprintf(
                'Latitude %s greater than allowed (%s). ',
                $value,
                self::GOOGLE_MAX_LAT
            );
        }
        if ('' != $errormsg)
        {
            throw new \DomainException(
                rtrim($errormsg)
            );
        }

        return true;
    }

    private function isValidLongitude($value)
    {
        $errormsg = '';
        if ($value < self::GOOGLE_MIN_LONG)
        {
            $errormsg .= sprintf(
                'Longitude %s lesser than allowed (%s). ',
                $value,
                self::GOOGLE_MIN_LONG
            );
        }
        if ($value > self::GOOGLE_MAX_LONG)
        {
            $errormsg .= sprintf(
                'Longitude %s greater than allowed (%s). ',
                $value,
                self::GOOGLE_MAX_LONG
            );
        }
        if ('' != $errormsg)
        {
            throw new \DomainException(
                rtrim($errormsg)
            );
        }

        return true;
    }

}