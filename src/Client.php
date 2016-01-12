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

use GuzzleHttp\Psr7\Uri;
use MetaSyntactical\Http\Transport\TransportInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

class Client implements LoggerAwareInterface
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
     * @var LoggerInterface;
     */
    private $logger;

    /**
     * @var string
     */
    private $serviceEndpoint = 'https://maps.googleapis.com/maps/api/directions/json?';

    /**
     * @var PolylineDecoderInterface
     */
    private $polylineDecoder;

    /**
     * @var TransportInterface
     */
    private $httpTransport;

    /**
     * Client constructor.
     *
     * @param string $apiKey Google API key
     * @param PolylineDecoderInterface $polylineDecoder
     */
    public function __construct(
        $apiKey,
        PolylineDecoderInterface $polylineDecoder,
        TransportInterface $httpTransport
    )
    {
        $this->apiKey = $apiKey;
        $this->polylineDecoder = $polylineDecoder;
        $this->httpTransport = $httpTransport;
    }

    /**
     * Adds interpolated route step by step on every call.
     *
     * @param Route $route
     * @return Route
     */
    public function getDirections(Route $route)
    {
        $request = $this->httpTransport->newRequest()
            ->withMethod('GET')
            ->withUri($this->createUri($route));
        try
        {
            $response = $this->httpTransport->send($request);
        }
        catch(\GuzzleHttp\Exception\RequestException $e)
        {
            if (!is_null($this->logger))
            {
                $this->logger->error($e->getMessage());
            }
            return $route;
        }

        if (200 != $response->getStatusCode())
        {
            $this->logger->error(
                sprintf(
                    'Received HTTP response code %s with reason "%s".',
                    $response->getStatusCode(),
                    $response->getReasonPhrase()
                )
            );
            return $route;
        }

        $result = json_decode($response->getBody()->getContents(), true);
        $overviewPolyline = $result['routes'][0]['overview_polyline']['points'];
        $coordinates = $this->polylineDecoder->decode($overviewPolyline);
        $route->addToInterpolatedRoute($coordinates);

        return $route;
    }

    /**
     * @param Route $route
     * @return Uri
     */
    private function createUri(Route $route)
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

        $uriString = $this->serviceEndpoint
            . "origin=$origLat,$origLng"
            . "&destination=$destLat,$destLng";

        $uriString .= $this->addWaypoints($waypoints);

        $uriString .= "&key=$this->apiKey";
        return new Uri($uriString);
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

    /**
     * Sets a logger instance on the object
     *
     * @param LoggerInterface $logger
     * @return void
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
