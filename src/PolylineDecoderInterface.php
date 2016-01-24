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


interface PolylineDecoderInterface
{
    /**
     * @param string $polyline
     * @return Coordinate[]
     */
    public function decode($polyline);
}
