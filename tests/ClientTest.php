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
     * @dataProvider getDirectionsProvider()
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
        self::assertEquals($initialCoordinateCount, count($result->getInputRoute()));
    }

    /**
     * @dataProvider getDirectionsProvider()
     */
    public function testGetDirections200ZeroResults($coordinates, $remainingCoordinateCount, $initialCoordinateCount)
    {
        $obj = new Client(
            'abcdefg',
            new Polyline(),
            $this->get200ZeroResultMock()
        );

        $obj->setLogger($this->logger);

        $routeFactory = new RouteFactory();
        $route = $routeFactory->createRoute($coordinates);

        $result = $obj->getDirections($route);

        self::assertTrue(
            $this->logger->hasRecord(
                'error Request "https://maps.googleapis.com/maps/api/directions/json?origin=50.1109756,8.6824697&destination=50.1320079,8.6829269&waypoints=via:50.1131057,8.6935646%7Cvia:50.1114651,8.704576%7Cvia:50.1128467,8.7049644%7Cvia:50.1173763,8.7084722%7Cvia:50.1292499,8.6924497&key=abcdefg" did not yield results.'
            )
        );
        self::assertEquals($route, $result);
    }

    /**
     * @dataProvider getDirectionsProvider()
     */
    public function testGetDirections200UnknownStatus($coordinates, $remainingCoordinateCount, $initialCoordinateCount)
    {
        $obj = new Client(
            'abcdefg',
            new Polyline(),
            $this->get200UnknownStatusMock()
        );

        $obj->setLogger($this->logger);

        $routeFactory = new RouteFactory();
        $route = $routeFactory->createRoute($coordinates);

        $result = $obj->getDirections($route);

        self::assertTrue(
            $this->logger->hasRecord(
                'error Request "https://maps.googleapis.com/maps/api/directions/json?origin=50.1109756,8.6824697&destination=50.1320079,8.6829269&waypoints=via:50.1131057,8.6935646%7Cvia:50.1114651,8.704576%7Cvia:50.1128467,8.7049644%7Cvia:50.1173763,8.7084722%7Cvia:50.1292499,8.6924497&key=abcdefg" did not return with status "OK" (status: "UNKNOWN").'
            )
        );
        self::assertEquals($route, $result);
    }

    /**
     * @dataProvider getDirectionsProvider()
     */
    public function testGetDirections200NoPolyline($coordinates, $remainingCoordinateCount, $initialCoordinateCount)
    {
        $obj = new Client(
            'abcdefg',
            new Polyline(),
            $this->get200NoPolylineMock()
        );

        $obj->setLogger($this->logger);

        $routeFactory = new RouteFactory();
        $route = $routeFactory->createRoute($coordinates);

        $result = $obj->getDirections($route);

        self::assertTrue(
            $this->logger->hasRecord(
                'error Request "https://maps.googleapis.com/maps/api/directions/json?origin=50.1109756,8.6824697&destination=50.1320079,8.6829269&waypoints=via:50.1131057,8.6935646%7Cvia:50.1114651,8.704576%7Cvia:50.1128467,8.7049644%7Cvia:50.1173763,8.7084722%7Cvia:50.1292499,8.6924497&key=abcdefg" did not yield polyline.'
            )
        );
        self::assertEquals($route, $result);
    }

    /**
     * @dataProvider getDirectionsProvider()
     */
    public function testGetDirections204($coordinates, $remainingCoordinateCount, $initialCoordinateCount)
    {
        $obj = new Client(
            'abcdefg',
            new Polyline(),
            $this->get204Mock()
        );

        $obj->setLogger($this->logger);

        $routeFactory = new RouteFactory();
        $route = $routeFactory->createRoute($coordinates);

        $result = $obj->getDirections($route);
        self::assertTrue(
            $this->logger->hasRecord(
                'error Received HTTP response code 204 with reason "No Content".'
            )
        );
        self::assertEquals($route, $result);
    }

    /**
     * @dataProvider getDirectionsProvider()
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
                . 'abcdefg` resulted in a `500 Internal Server Error` response'
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
                7
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

    private function get204Mock()
    {
        $mock = new MockHandler([
                new Response(204)
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
                        [
                            'status' => 'OK',
                            'routes' => [
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

    private function get200ZeroResultMock()
    {
        $mock = new MockHandler([
                new Response(
                    200,
                    [],
                    json_encode(
                        [
                            'status' => 'ZERO_RESULTS',
                            'routes' => [
                                0 => []
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

    private function get200UnknownStatusMock()
    {
        $mock = new MockHandler([
                new Response(
                    200,
                    [],
                    json_encode(
                        [
                            'status' => 'UNKNOWN',
                            'routes' => [
                                0 => []
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

    private function get200NoPolylineMock()
    {
        $mock = new MockHandler([
                new Response(
                    200,
                    [],
                    json_encode(
                        [
                            'status' => 'OK',
                            'routes' => [
                                0 => []
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
