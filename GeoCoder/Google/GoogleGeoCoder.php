<?php

/*
 * This file is part of Mindy Framework.
 * (c) 2017 Maxim Falaleev
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mindy\Bundle\GeoCoderBundle\GeoCoder\Google;

use GuzzleHttp\Client;
use Mindy\Bundle\GeoCoderBundle\GeoCoder\GeoCoderInterface;
use Mindy\Bundle\GeoCoderBundle\GeoCoder\GeoNotFoundException;
use Mindy\Bundle\GeoCoderBundle\GeoCoder\GeoResult;

class GoogleGeoCoder implements GeoCoderInterface
{
    protected $endpoint = 'https://maps.googleapis.com/maps/api/geocode/json?';
    protected $client;

    /**
     * GoogleGeocoder constructor.
     *
     * @param $apiKey
     * @param $language
     * @param $region
     */
    public function __construct($apiKey = null, $language = null, $region = null)
    {
        $this->client = new Client();

        if ($apiKey) {
            $this->endpoint = $this->buildQuery('key', $apiKey);
        }

        if ($language) {
            $this->endpoint = $this->buildQuery('language', $language);
        }

        if ($region) {
            $this->endpoint = $this->buildQuery('region', $region);
        }
    }

    /**
     * @param float $lat
     * @param float $lng
     *
     * @throws GeoNotFoundException
     *
     * @return string
     */
    public function getAddress($lat, $lng)
    {
        $query = $this->buildQuery('address', sprintf('%s,%s', $lat, $lng));
        $response = $this->validateResponse($this->getResponse($query));

        if (isset($response['results']) && count($response['results']) > 0) {
            $result = [];
            foreach ($response['results'] as $result) {
                $coordinates = $result['geometry']['location'];

                $result[] = (new GeoResult())
                    ->setLat($coordinates['lat'])
                    ->setLng($coordinates['lng'])
                    ->setAddress($result['formatted_address']);
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
        $query = $this->buildQuery('address', $address);
        $response = $this->validateResponse($this->getResponse($query));

        if (isset($response['results']) && count($response['results']) > 0) {
            $result = [];
            foreach ($response['results'] as $result) {
                $coordinates = $result['geometry']['location'];

                $result[] = (new GeoResult())
                    ->setLat($coordinates['lat'])
                    ->setLng($coordinates['lng'])
                    ->setAddress($result['formatted_address']);
            }

            return $result;
        }

        throw new GeoNotFoundException();
    }

    /**
     * Helper that adds an additional parameter to the query.
     *
     * @param $key
     * @param $value
     *
     * @return string
     */
    private function buildQuery($key, $value)
    {
        return sprintf('%s&%s=%s', $this->endpoint, $key, rawurlencode($value));
    }

    /**
     * Get the response from Google and decode it.
     *
     * @param $endpoint
     *
     * @return mixed
     */
    private function getResponse($endpoint)
    {
        return json_decode($this->client->get($endpoint)->getBody(), true);
    }

    /**
     * Makes sure the response does not contain any errors.
     *
     * @param $response
     *
     * @throws AccessDenied
     * @throws InvalidKey
     * @throws NoResult
     * @throws QuotaExceeded
     *
     * @return mixed
     */
    private function validateResponse($response)
    {
        if (!isset($response)) {
            throw new NoResult(sprintf('Could not execute query'));
        }

        if ('REQUEST_DENIED' === $response['status'] && 'The provided API key is invalid.' === $response['error_message']) {
            throw new InvalidKey(sprintf('API key is invalid'));
        }

        if ('REQUEST_DENIED' === $response['status']) {
            throw new AccessDenied(sprintf('API access denied. Message: %s', $response['error_message']));
        }

        // you are over your quota
        if ('OVER_QUERY_LIMIT' === $response['status']) {
            throw new QuotaExceeded('Daily quota exceeded');
        }

        // no result
        if (!isset($response['results']) || !count($response['results']) || 'OK' !== $response['status']) {
            throw new NoResult('No results for query');
        }

        return $response;
    }
}
