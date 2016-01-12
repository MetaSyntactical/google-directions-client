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
use PHPUnit_Framework_TestCase;

class RouteFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var RouteFactory
     */
    protected $object;

    /**
     * @var TestLogger
     */
    protected $logger;

    protected function setUp()
    {
        $this->object = new RouteFactory();
        $this->logger = new TestLogger();
    }

    /**
     * @dataProvider createRouteProvider
     */
    public function testCreateRoute($coordinates, $loggerMsg, $inputRouteCount)
    {
        $this->object->setLogger($this->logger);

        $route = $this->object->createRoute($coordinates);

        $inputRouteCoordinates = $route->getInputRoute();
        self::assertEquals($inputRouteCount, count($inputRouteCoordinates));
        foreach ($inputRouteCoordinates as $coordinate)
        {
            self::assertTrue($coordinate instanceof Coordinate);
        }

        if ($loggerMsg)
        {
            self::assertTrue($this->logger->hasRecord($loggerMsg));
        }

    }

    public function createRouteProvider()
    {
        return [
            [['13.30841,52.54628', '13.30837,52.54633'], false, 2],
            [['13.30841,52.54628', '13.30837,52.54633', '13.30894,52.5477', '13.3066,52.54785', '13.29709,52.54804'], false, 5],
            [['13.30841,52.54628', '13.30837,52.54633', '85.54321,179.78901'], 'Given coordinates "85.54321,179.78901" are invalid. Latitude 85.54321 greater than allowed (85).', 2], //third coordinate has invalid latitude and longitude
            [['13.30841,52.54628', '13.30837,52.54633', '84.54321,185.78901'], 'Given coordinates "84.54321,185.78901" are invalid. Longitude 185.78901 greater than allowed (180).', 2], //third coordinate has invalid latitude and longitude
            [['13.30841,52.54628', '13.30837,52.54633', '-85.54321,-179.78901'], 'Given coordinates "-85.54321,-179.78901" are invalid. Latitude -85.54321 lesser than allowed (-85).', 2], //third coordinate has invalid latitude and longitude
            [['13.30841,52.54628', '13.30837,52.54633', '-84.54321,-185.78901'], 'Given coordinates "-84.54321,-185.78901" are invalid. Longitude -185.78901 lesser than allowed (-180).', 2], //third coordinate has invalid latitude and longitude
            [['lorem ipsum'], '"lorem ipsum" are not valid coordinates.', 0],
            [['lorem, ipsum'], 'Given coordinates "lorem, ipsum" are invalid. Value "lorem" is not numeric.', 0]
        ];
    }
}
