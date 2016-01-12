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


use Gamez\Psr\Log\TestLogger;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use MetaSyntactical\Http\Transport\Guzzle\GuzzleTransport;
use PHPUnit_Framework_TestCase;

class ClientTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    private $container = [];

    /**
     * @var TestLogger
     */
    private $logger;

    protected function setUp()
    {
        $this->logger = new TestLogger();
    }

    /**
     * @dataProvider getDirectionsProvider
     */
    public function testGetDirections200($coordinates, $remainingCoordinateCount, $initialCoordinateCount)
    {
        $obj = new Client(
            'abcdefg',
            new Polyline(),
            $this->get200Mock()
        );

        $routeFactory = new RouteFactory();
        $route = $routeFactory->createRoute($coordinates);

        $result = $obj->getDirections($route);

        self::assertEquals(163, count($result->getInterpolatedRoute()));
        self::assertEquals(0, $result->getRemainingCoordinateCount());
    }

    /**
     * @dataProvider getDirectionsProvider
     */
    public function testGetDirections500($coordinates, $remainingCoordinateCount, $initialCoordinateCount)
    {
        $obj = new Client(
            'abcdefg',
            new Polyline(),
            $this->get500Mock()
        );

        $obj->setLogger($this->logger);

        $routeFactory = new RouteFactory();
        $route = $routeFactory->createRoute($coordinates);

        $result = $obj->getDirections($route);
        self::assertTrue(
            $this->logger->hasRecord(
                'error Server error: `GET https://maps.googleapis.com/maps/api/directions/json?origin=50.1109756,'
                . '8.6824697&destination=50.1320079,8.6829269&waypoints=via:50.1131057,8.6935646%7Cvia:50.1114651,'
                . '8.704576%7Cvia:50.1128467,8.7049644%7Cvia:50.1173763,8.7084722%7Cvia:50.1292499,8.6924497&key='
                . 'abcdefg` resulted in a `500 Internal Server Error` response:'
            )
        );
        self::assertEquals($route, $result);
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

    private function get500Mock()
    {
        $mock = new MockHandler([
                new Response(500)
            ]);
        $stack = HandlerStack::create($mock);

        $history = Middleware::history($this->container);
        $stack->push($history);

        $client = new HttpClient(["handler" => $stack]);

        return new GuzzleTransport($client);
    }

    private function get200Mock()
    {
        $mock = new MockHandler([
                new Response(
                    200,
                    [],
                    json_encode(
                        ['routes' => [
                            0 => [
                                'overview_polyline' => [
                                    'points' => "yhzpHgx~s@u@}Cc@gDYyAxAm@DK?EF_@Du@?i@OuCEo@Eq@WNiClBWPYJK?I?IuAM}FIeBMqCCkAB}AAaFEq@Gg@eAuE[_BGw@IyCYmE?mDLeC`@aGZmHNiDN}E@_BKoF@o@Ly@FO`@i@nAoATOjDiAUyBK{B]e@_DeCk@o@m@YOhBA|@Ad@@ZGv@IrAGnCAd@`B|@HFFDp@q@TOjDiAUyBK{B]e@_DeCk@o@m@YUQMSqA{BSSSE]HOAe@m@IOKEsBiD}@kAW[GOa@a@i@e@MGYCS?a@R{@~@oE|Fa@`@o@`@sC`DaC|CmBzBm@z@sApBcBrB_Ap@U\\_@n@MZY|@[p@oAbB_@d@U^WRi@n@y@bAcAnAIP}A`Bi@n@kAzAkAtAi@d@qAt@kAvAkBlCyC|EcDnGIPNbBf@fDHXZpD^hDBn@NzBD`@qDv@mIxAJaAJk@lAgE`BeFdBsEf@kACKIWWSeBfEo@hBc@jAkArD{AvFcAbEu@dDeCzNe@rBG^AbABfDIhB[|BMnA"
                                    ]
                                ]
                            ]
                        ]
                    )
                )
            ]);
        $stack = HandlerStack::create($mock);

        $history = Middleware::history($this->container);
        $stack->push($history);

        $client = new HttpClient(["handler" => $stack]);

        return new GuzzleTransport($client);
    }
}