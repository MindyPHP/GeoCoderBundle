<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\GeoCoderBundle\GeoCoder\Yandex;

use Mindy\Bundle\GeoCoderBundle\GeoCoder\GeoCoderInterface;
use Mindy\Bundle\GeoCoderBundle\GeoCoder\GeoNotFoundException;
use Mindy\Bundle\GeoCoderBundle\GeoCoder\GeoResult;
use Yandex\Geo\Api;

class YandexGeoCoder implements GeoCoderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getAddress($lat, $lng)
    {
        $api = (new Api())
            ->setPoint($lat, $lng)
            ->setLimit(1)
            ->setLang(Api::LANG_RU)
            ->load();

        $response = $api->getResponse();
        if ($response->getFoundCount() > 0) {
            $result = [];
            foreach ($response->getList() as $item) {
                $result[] = (new GeoResult())
                    ->setAddress($item->getAddress())
                    ->setLat($item->getLatitude())
                    ->setLng($item->getLongitude());
            }

            return $result;
        }

        throw new GeoNotFoundException();
    }

    /**
     * {@inheritdoc}
     */
    public function getCoordinates($address)
    {
        $api = (new Api())
            ->setQuery($address)
            ->setLimit(1)
            ->setLang(Api::LANG_RU)
            ->load();

        $response = $api->getResponse();
        if ($response->getFoundCount() > 0) {
            $result = [];
            foreach ($response->getList() as $item) {
                $result[] = (new GeoResult())
                    ->setAddress($item->getAddress())
                    ->setLat($item->getLatitude())
                    ->setLng($item->getLongitude());
            }

            return $result;
        }

        throw new GeoNotFoundException();
    }
}
