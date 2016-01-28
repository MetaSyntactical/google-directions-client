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
    public function testGetDirections(
        $coordinates, $remainingCountAfterFirstRequest, $initialRemainingCoordinates, $expectedNoRequests
    )
    {
        $actualNoRequests = 0;
        $routeFactory = new RouteFactory();
        $route = $routeFactory->createRoute($coordinates);

        self::assertEquals($initialRemainingCoordinates, $route->getRemainingCoordinateCount());

        while($route->getRemainingCoordinateCount())
        {
            $actual = $this->object->getDirections($route);
            $actualNoRequests++;
            if (1 == $actualNoRequests)
            {
                self::assertEquals($remainingCountAfterFirstRequest, $actual->getRemainingCoordinateCount());
            }
        }
        self::assertEquals($expectedNoRequests, $actualNoRequests);
        self::assertEquals(0, $actual->getRemainingCoordinateCount());
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
                6,
                1
            ],
            [
                [
                    '50.1109756,8.6824697',  // Römer
                    '50.1131057,8.6935646',  // Allerheiligentor
                    '50.1114651,8.704576',   // "Frankfurter Küche"
                    '50.1128467,8.7049644',  // Ostbahnhof
                    '50.1173763,8.7084722',  // Habsburgerallee
                    '50.1292499,8.6924497',  // Nibelungenplatz/FH
                    '50.1320079,8.6829269',  // Deutsche Nationalbibliothek
                    '50.131774,8.672302',    // Adickesallee/Eschersheimer Landstraße
                    '50.128614, 8.672731',   // Holzhausenschule
                    '50.126392, 8.670925',   // Goethe-Universität
                    '50.1168304,8.6681944',  // Alte Oper
                    '50.110276, 8.662673',   // Westend Tower
                    '50.107456, 8.664766',   // Hauptbahnhof
                    '50.101777, 8.665700',   // Friedensbrücke
                    '50.099600, 8.673030',   // Rubensstraße/Kennedyallee
                    '50.101856, 8.676202',   // Morgensternstraße
                    '50.100451, 8.681827',   // Schweizer Straße
                    '50.101262, 8.685687',   // Textorstraße
                    '50.100623, 8.691451',   // Darmstädter Landstraße 79
                    '50.103538, 8.695242',   // Siemensstraße 18
                    '50.107623, 8.694887',   // Flößerbrücke
                    '50.110981, 8.703377',   // EZB
                    '50.111330, 8.711697',   // Osthafenplatz
                    '50.114860, 8.722239',   // Hanauer Landstraße 184
                    '50.118532, 8.731702',   // Hanauer Landstraße / A661
                    '50.121535, 8.728733',   // Ratsweg 10
                    '50.123178, 8.721735',   // Festplatz / Dippemess
                    '50.124605, 8.713721',   // Saalburgstraße / Wittelsbacher Allee
                    '50.126384, 8.708057',   // Bornheim Mitte
                    '50.127925, 8.702243',   // IGS Nordend
                    '50.130967, 8.698868',   // Wartburgkirche
                    '50.133372, 8.695542',   // Lidl Friedberger Landstraße
                    '50.140766, 8.698928',   // Friedberger Warte
                ],
                7,
                32,
                2
            ]
        ];
    }
}
