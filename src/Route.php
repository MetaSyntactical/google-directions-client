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

class Route
{
    /**
     * @var Coordinate[]
     */
    private $inputRoute = [];

    /**
     * @var Coordinate[]
     */
    private $interpolatedRoute = [];

    /**
     * @var Coordinate[]
     */
    private $remainingCoordinates = [];

    /**
     * @var Coordinate
     */
    private $current;

    /**
     * @return Coordinate[]
     */
    public function getInputRoute()
    {
        return $this->inputRoute;
    }

    /**
     * @param Coordinate[] $inputRoute
     * @return Route
     */
    public function setInputRoute($inputRoute)
    {
        $this->inputRoute = $inputRoute;
        $this->current = array_shift($inputRoute);
        $this->remainingCoordinates = $inputRoute;

        return $this;
    }

    /**
     * @return Coordinate
     */
    public function getCurrentCoordinate()
    {
        return $this->current;
    }

    /**
     * Returns false if there are no more remaining coordinates.
     *
     * @return Coordinate|bool
     */
    public function getNextCoordinate()
    {
        if (empty($this->remainingCoordinates))
        {
            return false;
        }

        $this->current = array_shift($this->remainingCoordinates);
        return $this->current;
    }

    /**
     * @return Coordinate[]
     */
    public function getInterpolatedRoute()
    {
        return $this->interpolatedRoute;
    }

    /**
     * @return int
     */
    public function getRemainingCoordinateCount()
    {
        return count($this->remainingCoordinates);
    }

    /**
     * @param Coordinate[] $coordinates
     * @return Route
     */
    public function addToInterpolatedRoute($coordinates)
    {
        Assertion::allIsInstanceOf($coordinates, 'MetaSyntactical\GoogleDirections\Coordinate');
        $this->interpolatedRoute = array_merge($this->interpolatedRoute, $coordinates);

        return $this;
    }
}