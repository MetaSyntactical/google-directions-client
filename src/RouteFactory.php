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
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

class RouteFactory implements LoggerAwareInterface
{
    /**
     * @var LoggerInterface;
     */
    private $logger;

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
                if(!is_null($this->logger))
                {
                    $this->logger->error(
                        sprintf('"%s" are not valid coordinates.', $item)
                    );
                }
                continue;
            }
            try
            {
                Assertion::allNumeric($valueArray);
            }
            catch (AssertionFailedException $e)
            {
                if (!is_null($this->logger))
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
            }
            $lat = (float)$valueArray[0];
            $long = (float)$valueArray[1];
            try
            {
                $coordinate = new Coordinate($lat, $long);
            }
            catch (\DomainException $e)
            {
                if (!is_null($this->logger))
                {
                    $this->logger->error(
                        sprintf(
                            'Given coordinates "%s" are invalid. %s',
                            $item,
                            $e->getMessage()
                        )
                    );
                }
                continue;
            }

            $coordinates[] = $coordinate;
        }

        $route = new Route();
        $route->setInputRoute($coordinates);

        return $route;
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