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


use Assert\Assertion;
use PHPUnit_Framework_TestCase;

class ClientTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Client
     */
    protected $object;

    protected function setUp()
    {
        $configIni = __DIR__ . '/test.config.ini';
        if (file_exists($configIni))
        {
            $cofig = parse_ini_file($configIni, true);
            $polylineDecoder = new Polyline();
            // For usage in production it might be useful to log which
            // coordinates were skipped, because of errors (e.g. if coordinate
            // was invalid).
            //$polylineDecoder->setLogger($loggerObj);
            $this->object = new Client($cofig['Google']['ApiKey'], $polylineDecoder);
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