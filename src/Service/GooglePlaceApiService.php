<?php

namespace App\Service;

use App\Entity\GasStation;
use Exception;
use GuzzleHttp\Client;
use Safe;

class GooglePlaceApiService
{
    const TEXT_SEARCH_URL = 'https://maps.googleapis.com/maps/api/place/textsearch/json?query=%s&key=%s&type=gas_station';
    const PLACE_DETAILS_URL = 'https://maps.googleapis.com/maps/api/place/details/json?place_id=%s&key=%s';

    private Client $client;

    public function __construct(
        private string $key
    )
    {
        $this->client = new Client();
    }

    /**
     * @return array<mixed>|null
     */
    public function textSearch(GasStation $gasStation)
    {
        $response = $this->client->request("GET", sprintf(self::TEXT_SEARCH_URL, $gasStation->getAddress()->getStreet(), $this->key));
        $response = Safe\json_decode($response->getBody()->getContents(), true);

        if ($this->checkStatus($response, self::TEXT_SEARCH_URL) && array_key_exists('results', $response) && count($response['results']) > 0 && array_key_exists('place_id', $response['results'][0])) {
            return $response['results'][0];
        }

        return null;
    }

    private function checkStatus(array $response, string $method): bool
    {
        if (!array_key_exists('status', $response) || (array_key_exists('status', $response) && in_array($response['status'], ['INVALID_REQUEST', 'OVER_QUERY_LIMIT', 'REQUEST_DENIED', 'UNKNOWN_ERROR']))) {
            throw new Exception(sprintf('Invalid response status for %s : %s', $method, $response['status'] ?? 'unknown'));
        }

        return true;
    }

    /**
     * @return array<mixed>|null
     */
    public function placeDetails(GasStation $gasStation)
    {
        $response = $this->client->request("GET", sprintf(self::PLACE_DETAILS_URL, $gasStation->getGooglePlace()->getPlaceId(), $this->key));
        $response = Safe\json_decode($response->getBody()->getContents(), true);

        if ($this->checkStatus($response, self::PLACE_DETAILS_URL) && array_key_exists('result', $response) && $response['status'] === 'OK' && count($response['result']) > 0) {
            return $response['result'];
        }

        return null;
    }
}
