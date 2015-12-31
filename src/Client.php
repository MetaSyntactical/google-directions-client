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


class Client
{
    /**
     * The Google directions API is limited to handling 23 waypoints per request.
     */
    const MAX_WAYPOINTS_LIMIT = 23;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $serviceEndpoint = 'https://maps.googleapis.com/maps/api/directions/json?';

    /**
     * @var PolylineDecoderInterface
     */
    private $polylineDecoder;

    /**
     * Client constructor.
     *
     * @param string $apiKey Google API key
     * @param PolylineDecoderInterface $polylineDecoder
     */
    public function __construct($apiKey, PolylineDecoderInterface $polylineDecoder)
    {
        $this->apiKey = $apiKey;
        $this->polylineDecoder = $polylineDecoder;
    }

    /**
     * Adds interpolated route step by step on every call.
     *
     * @param Route $route
     * @return Route
     */
    public function getDirections(Route $route)
    {
        $request = $this->createRequest($route);
        //@TODO introduce Guzzle to make request and add error handling
        $result = json_decode(file_get_contents($request), true);
        $overviewPolyline = $result['routes'][0]['overview_polyline']['points'];
        $coordinates = $this->polylineDecoder->decode($overviewPolyline);
        $route->addToInterpolatedRoute($coordinates);

        return $route;
    }

    /**
     * @param Route $route
     * @return string
     */
    private function createRequest(Route $route)
    {
        $cnt = 1;
        $max = self::MAX_WAYPOINTS_LIMIT + 2;
        $start = null;
        $waypoints = [];
        $end = null;

        $start = $route->getCurrentCoordinate();

        while (($coordinate = $route->getNextCoordinate()) && $cnt <= $max)
        {
            $waypoints[] = $coordinate;
            $cnt++;
        }

        $end = array_pop($waypoints);

        $origLat = $start->getLatitude();
        $origLng = $start->getLongitude();
        $destLat = $end->getLatitude();
        $destLng = $end->getLongitude();

        $request = $this->serviceEndpoint
            . "origin=$origLat,$origLng"
            . "&destination=$destLat,$destLng";

        $request .= $this->addWaypoints($waypoints);

        $request .= "&key=$this->apiKey";
        return $request;
    }

    /**
     * @param Coordinate[] $waypoints
     * @return string
     */
    private function addWaypoints($waypoints)
    {
        $getParams = '';
        $first = true;

        foreach ($waypoints as $waypoint)
        {
            if ($first)
            {
                $getParams = '&waypoints=';
                $first = false;
            }
            $getParams .= 'via:';
            $getParams .= $waypoint->getLatitude();
            $getParams .= ',';
            $getParams .= $waypoint->getLongitude();
            $getParams .= '|';
        }

        return rtrim($getParams, '|');
    }
}
