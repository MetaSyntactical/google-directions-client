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


class Route
{
    /**
     * @var Coordinate[]
     */
    private $inputRoute;

    /**
     * @var Coordinate[]
     */
    private $interpolatedRoute;

    /**
     * @var Coordinate[]
     */
    private $remainingCoordinates;

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
        return $this;
    }

    /**
     * @return Coordinate[]
     */
    public function getInterpolatedRoute()
    {
        return $this->interpolatedRoute;
    }

    /**
     * @param Coordinate[] $interpolatedRoute
     * @return Route
     */
    private function setInterpolatedRoute($interpolatedRoute)
    {
        $this->interpolatedRoute = $interpolatedRoute;
        return $this;
    }

    /**
     * @return Coordinate[]
     */
    private function getRemainingCoordinates()
    {
        return $this->remainingCoordinates;
    }

    /**
     * @param Coordinate[] $remainingCoordinates
     * @return Route
     */
    private function setRemainingCoordinates($remainingCoordinates)
    {
        $this->remainingCoordinates = $remainingCoordinates;
        return $this;
    }

    /**
     * @return int
     */
    public function getRemainingCoordinateCount()
    {
        return count($this->remainingCoordinates);
    }
}