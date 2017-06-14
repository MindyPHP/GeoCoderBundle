<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\GeoCoderBundle\Library;

use Mindy\Bundle\GeoCoderBundle\GeoCoder\GeoCoderInterface;
use Mindy\Bundle\GeoCoderBundle\GeoCoder\GeoNotFoundException;
use Mindy\Template\Library;

class GeoCoderLibrary extends Library
{
    /**
     * @var GeoCoderInterface
     */
    protected $geoCoder;

    /**
     * GeoCoderLibrary constructor.
     *
     * @param GeoCoderInterface $geoCoder
     */
    public function __construct(GeoCoderInterface $geoCoder)
    {
        $this->geoCoder = $geoCoder;
    }

    /**
     * @return array
     */
    public function getHelpers()
    {
        return [
            'geo_address' => function ($lat, $lng) {
                try {
                    return $this->geoCoder->getAddress($lat, $lng);
                } catch (GeoNotFoundException $e) {
                    return [];
                }
            },
            'geo_coordinates' => function ($address) {
                try {
                    return $this->geoCoder->getCoordinates($address);
                } catch (GeoNotFoundException $e) {
                    return [];
                }
            },
        ];
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return [];
    }
}
