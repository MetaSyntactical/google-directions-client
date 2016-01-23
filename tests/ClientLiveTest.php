<?php
/**
 * This file is part of google-directions-client.
 *
 * (c) David Weichert <info@davidweichert.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MetaSyntactical\GoogleDirections;


use GuzzleHttp\Client as HttpClient;
use MetaSyntactical\Http\Transport\Guzzle\GuzzleTransport;
use PHPUnit_Framework_TestCase;

/**
 * Class ClientLiveTest
 * @package MetaSyntactical\GoogleDirections
 *
 * This test demonstrates the usage of the client against the Google Directions
 * API. It will actually call the Google API using the API key read from the
 * environment variable API_KEY. If the environment variable is not present this
 * test is skipped.
 */
class ClientLiveTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Client
     */
    protected $object;

    protected function setUp()
    {
        $googleApiKey = getenv('API_KEY');

        if ($googleApiKey)
        {
            $polylineDecoder = new Polyline();
            $this->object = new Client(
                $googleApiKey, $polylineDecoder, new GuzzleTransport(new HttpClient())
            );
        }
        else
        {
            self::markTestSkipped(
                'Test configuration missing. See: tests/test.config.ini.dist.'
            );
        }
    }

    /**
     * @dataProvider getDirectionsProvider
     */
    public function testGetDirections($coordinates, $remainingCoordinateCount, $initialCoordinateCount)
    {
        $routeFactory = new RouteFactory();
        $route = $routeFactory->createRoute($coordinates);

        self::assertEquals($initialCoordinateCount, $route->getRemainingCoordinateCount());
        $actual = $this->object->getDirections($route);
        self::assertEquals($remainingCoordinateCount, $actual->getRemainingCoordinateCount());
        self::assertTrue(is_array($route->getInterpolatedRoute()));
    }

    public function getDirectionsProvider()
    {
        return [
            [
                [
                    '50.1109756,8.6824697',  // Römer
                    '50.1131057,8.6935646',  // Allerheiligentor
                    '50.1114651,8.704576',   // "Frankfurter Küche"
                    '50.1128467,8.7049644',  // Ostbahnhof
                    '50.1173763,8.7084722',  // Habsburgerallee
                    '50.1292499,8.6924497',  // Nibelungenplatz/FH
                    '50.1320079,8.6829269',  // Deutsche Nationalbibliothek
                ],
                0,
                6
            ]
        ];
    }
}