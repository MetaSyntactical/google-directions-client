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


use Assert\Assertion;
use Assert\AssertionFailedException;
use DomainException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

class RouteFactory implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct()
    {
        $this->logger = new NullLogger();
    }

    /**
     * Create Route.
     *
     * Creates a Route object from an array of comma separated coordinates,
     * e.g. ['52.54628,13.30841', '51.476780,0.000479', ...].
     *
     * @param string[] $arrayOfCommaSeparatedCoordinates
     * @return Route
     */
    public function createRoute($arrayOfCommaSeparatedCoordinates)
    {
        $coordinates = [];
        foreach ($arrayOfCommaSeparatedCoordinates as $item)
        {
            $valueArray = explode(',', $item);
            if (2 != count($valueArray))
            {
                $this->logger->error(
                    sprintf('"%s" are not valid coordinates.', $item)
                );
                continue;
            }
            try
            {
                Assertion::allNumeric($valueArray);
            }
            catch (AssertionFailedException $e)
            {
                $this->logger->error(
                    sprintf(
                        'Given coordinates "%s" are invalid. %s',
                        $item,
                        $e->getMessage()
                    )
                );
                continue;
            }
            $lat = (float)$valueArray[0];
            $long = (float)$valueArray[1];
            try
            {
                $coordinate = new Coordinate($lat, $long);
            }
            catch (DomainException $e)
            {
                $this->logger->error(
                    sprintf(
                        'Given coordinates "%s" are invalid. %s',
                        $item,
                        $e->getMessage()
                    )
                );
                continue;
            }

            $coordinates[] = $coordinate;
        }

        $route = new Route();
        $route->setInputRoute($coordinates);

        return $route;
    }
}
