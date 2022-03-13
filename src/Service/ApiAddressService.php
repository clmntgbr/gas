<?php

namespace App\Service;

use App\Entity\GasStation;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Response;

class ApiAddressService
{
    const API_ADDRESS_URL = 'https://api-adresse.data.gouv.fr/search/?q=%s&limit=1';

    public function update(GasStation $gasStation)
    {
        $client = new Client();

        $response = $client->request(
            "GET",
            sprintf(self::API_ADDRESS_URL, trim(strtolower(str_replace([',', 'France'], '', $gasStation->getAddress()->getStreet()))))
        );

        $content = json_decode($response->getBody()->getContents(), true);

        if (Response::HTTP_OK !== $response->getStatusCode()) {
            return;
        }

        if (array_key_exists('features', $content) && count($content['features']) > 0) {
            $result = $content['features'][0];
            if (array_key_exists('properties', $result) && array_key_exists('score', $result['properties'])) {
                if ($result['properties']['score'] > 0.85) {
                    $this->updateAddress($gasStation, $result);
                }
            }
        }
    }

    private function updateAddress(GasStation $gasStation, array $data)
    {
        $address = $gasStation->getAddress();

        if (array_key_exists('geometry', $data) && array_key_exists('coordinates', $data['geometry']) && count($data['geometry']['coordinates']) > 1) {
            $address
                ->setLongitude($data['geometry']['coordinates'][0])
                ->setLatitude($data['geometry']['coordinates'][1]);
        }
    }
}
