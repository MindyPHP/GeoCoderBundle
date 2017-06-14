<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\GeoCoderBundle\GeoCoder\Chain;

use Mindy\Bundle\GeoCoderBundle\GeoCoder\GeoCoderInterface;
use Mindy\Bundle\GeoCoderBundle\GeoCoder\GeoNotFoundException;

/**
 * Class ChainGeoCoder
 */
class ChainGeoCoder implements GeoCoderInterface
{
    /**
     * @var GeoCoderInterface[]
     */
    protected $geoCoders = [];

    /**
     * @param GeoCoderInterface $geoCoder
     */
    public function addGeoCoder(GeoCoderInterface $geoCoder)
    {
        $this->geoCoders[] = $geoCoder;
    }

    /**
     * {@inheritdoc}
     */
    public function getAddress($lat, $lng)
    {
        foreach ($this->geoCoders as $geoCoder) {
            try {
                return $geoCoder->getAddress($lat, $lng);
            } catch (GeoNotFoundException $e) {
                continue;
            }
        }

        throw new GeoNotFoundException();
    }

    /**
     * {@inheritdoc}
     */
    public function getCoordinates($address)
    {
        foreach ($this->geoCoders as $geoCoder) {
            try {
                return $geoCoder->getCoordinates($address);
            } catch (GeoNotFoundException $e) {
                continue;
            }
        }

        throw new GeoNotFoundException();
    }
}
