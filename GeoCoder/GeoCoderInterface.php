<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\GeoCoderBundle\GeoCoder;

interface GeoCoderInterface
{
    /**
     * @param float $lat
     * @param float $lng
     *
     * @throws GeoNotFoundException
     *
     * @return GeoResult[]
     */
    public function getAddress($lat, $lng);

    /**
     * @param string $address
     *
     * @throws GeoNotFoundException
     *
     * @return GeoResult[]
     */
    public function getCoordinates($address);
}
