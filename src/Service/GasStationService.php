<?php

namespace App\Service;

use App\Common\EntityId\GasStationId;
use App\Common\Exception\GasStationException;
use App\Entity\GasStation;
use App\Helper\GasStationStatusHelper;
use App\Lists\GasStationStatusReference;
use App\Message\CreateGasStationMessage;
use App\Message\UpdateGasStationIsClosedMessage;
use App\Repository\GasPriceRepository;
use App\Repository\GasStationRepository;
use DateInterval;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Safe;
use Safe\DateTimeImmutable;
use SimpleXMLElement;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\MessageBusInterface;

final class GasStationService
{
    const PREVIEW_GAS_STATIONS_PATH = "img/gas_stations/";
    const PREVIEW_GAS_STATIONS_NAME = "total.jpg";

    public function __construct(
        private MessageBusInterface    $messageBus,
        private GasStationStatusHelper $gasStationStatusHelper,
        private GasStationRepository   $gasStationRepository,
        private GasPriceRepository     $gasPriceRepository
    )
    {
    }

    public function getGasStationId(SimpleXMLElement $element): GasStationId
    {
        $gasStationId = (string)$element->attributes()->id;

        if (empty($gasStationId)) {
            throw new GasStationException(GasStationException::GAS_STATION_ID_EMPTY);
        }

        return new GasStationId($gasStationId);
    }

    /**
     * @param array<mixed> $element
     */
    public function isGasStationClosed(array $element, GasStation $gasStation): void
    {
        if (isset($element['fermeture']['attributes']['type']) && "D" == $element['fermeture']['attributes']['type']) {
            $gasStation
                ->setClosedAt(DateTimeImmutable::createFromFormat('Y-m-d H:i:s', str_replace("T", " ", substr($element['fermeture']['attributes']['debut'], 0, 19))));
        }
    }

    public function createGasStation(GasStationId $gasStationId, SimpleXMLElement $element): void
    {
        $this->messageBus->dispatch(new CreateGasStationMessage(
            $gasStationId,
            (string)$element->attributes()->pop,
            (string)$element->attributes()->cp,
            (string)$element->attributes()->longitude,
            (string)$element->attributes()->latitude,
            (string)$element->adresse,
            (string)$element->ville,
            "FRANCE",
            Safe\json_decode(str_replace("@", "", Safe\json_encode($element)), true)
        ), [new AmqpStamp('async-priority-high', AMQP_NOPARAM, [])]);
    }

    public function getGasStationInformationFromGovernment(GasStation $gasStation): void
    {
        $client = new Client();

        $options = [
            'headers' => [
                "authority" => "www.prix-carburants.gouv.fr",
                "content-length" => "0",
                "accept" => "text/javascript, text/html, application/xml, text/xml, */*",
                "x-prototype-version" => "1.7",
                "x-requested-with" => "XMLHttpRequest",
                "user-agent" => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36",
                "content-type" => "application/x-www-form-urlencoded; charset=UTF-8",
                "origin" => "https://www.prix-carburants.gouv.fr",
                "sec-fetch-site" => "same-origin",
                "sec-fetch-mode" => "cors",
                "sec-fetch-dest" => "empty",
                "referer" => "https://www.prix-carburants.gouv.fr/",
                "accept-language" => "fr-FR,fr;q=0.9,en-US;q=0.8,en;q=0.7,pt;q=0.6,de-DE;q=0.5,de;q=0.4,ru;q=0.3,vi;q=0.2,la;q=0.1,es;q=0.1",
                "cookie" => "PHPSESSID=74qmi76d5k6vk4uhal69k0qhf6; device_view=full; cookie_law=true; device_view=full"
            ]
        ];

        try {
            $response = $client->request("GET",
                sprintf("https://www.prix-carburants.gouv.fr/map/recuperer_infos_pdv/%s", $gasStation->getId()),
                $options
            );
        } catch (GuzzleException $e) {
            throw new GasStationException(GasStationException::GAS_STATION_INFORMATION_NOT_FOUND);
        }

        $content = $response->getBody()->getContents();

        if ('No route found' === $content) {
            return;
        }

        $values = trim(strip_tags(str_replace("\n", '/break/', $content)));
        $values = explode('/break/', $values);
        $values = array_map('trim', $values);
        $values = array_filter($values);

        if (isset($values[5]) && isset($values[6]) && isset($values[7]) && isset($values[8])) {
            $gasStation
                ->setName(trim($values[5]))
                ->setCompany(trim($values[6]));

            $address = $gasStation->getAddress();
            $address
                ->setStreet(sprintf('%s, %s, France', trim($values[7]), trim($values[8])))
                ->setVicinity(sprintf('%s, %s, France', trim($values[7]), trim($values[8])));

            $this->gasStationStatusHelper->setStatus(GasStationStatusReference::FOUND_ON_GOV_MAP, $gasStation);
        }
    }

    public function updateGasStationsClosed(): void
    {
        $gasStations = $this->gasStationRepository->findGasStationStatusNotClosed();

        foreach ($gasStations as $gasStation) {
            $result = $this->gasPriceRepository->getGasPriceCountByGasStation($gasStation);
            if (array_key_exists('gas_price_count', $result) && $result['gas_price_count'] == 0) {
                $this->gasStationIsClosedMessageDispatch($gasStation);
                continue;
            }

            $date = ((new Safe\DateTime('now'))->sub(new DateInterval('P6M')));
            $gasPrice = $this->gasPriceRepository->findLastGasPriceByGasStation($gasStation);
            if ($date > $gasPrice->getDate()) {
                $this->gasStationIsClosedMessageDispatch($gasStation);
            }
        }
    }

    private function gasStationIsClosedMessageDispatch(GasStation $gasStation): void
    {
        $this->messageBus->dispatch(new UpdateGasStationIsClosedMessage(
            new GasStationId($gasStation->getId())
        ), [new AmqpStamp('async-priority-high', AMQP_NOPARAM, [])]);
    }
}
