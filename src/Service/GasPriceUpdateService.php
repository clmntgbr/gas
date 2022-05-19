<?php

namespace App\Service;

use App\Common\EntityId\GasStationId;
use App\Common\EntityId\GasTypeId;
use App\Entity\GasStation;
use App\Repository\GasServiceRepository;
use App\Repository\GasStationRepository;
use App\Repository\GasTypeRepository;
use Safe;
use SimpleXMLElement;

final class GasPriceUpdateService
{
    const PATH = 'public/gas_price/';
    const FILENAME = 'gas-price.zip';

    public function __construct(
        private GasPriceService      $gasPriceService,
        private GasStationService    $gasStationService,
        private GasServiceService    $gasServiceService,
        private GasStationRepository $gasStationRepository,
        private GasServiceRepository $gasServiceRepository,
        private GasTypeRepository    $gasTypeRepository
    )
    {
    }

    public function update(?string $year = null): void
    {
        $gasStations = $this->gasStationRepository->findGasStationById();
        $gasServices = $this->gasServiceRepository->findGasServiceByGasStationId();
        $gasTypes = $this->gasTypeRepository->findGasTypeById();

        $xmlPath = $this->getXmlPath($year);

        $elements = Safe\simplexml_load_file($xmlPath);

        foreach ($elements as $element) {
            $gasStationId = $this->gasStationService->getGasStationId($element);

            if (!in_array(substr((string)$gasStationId->getId(), 0, 2), ['94'])) {
                continue;
            }

            if (!array_key_exists($gasStationId->getId(), $gasStations)) {
                $this->gasStationService->createGasStation($gasStationId, $element);
                $gasStations[$gasStationId->getId()] = [
                    "id" => $gasStationId->getId()
                ];
            }

            $this->getGasService($gasStationId, $element, $gasServices);
            $this->getGasPrices($gasStationId, $element, $gasTypes);
        }

        FileSystemService::delete($xmlPath);
    }

    private function getXmlPath(?string $year): string
    {
        if (null === $year) {
            return $this->gasPriceService->downloadGasPriceFile(
                self::PATH,
                self::FILENAME,
                GasPriceService::GAS_PRICE_FILE_TYPE
            );
        }

        return $this->gasPriceService->downloadGasPriceYearFile(
            self::PATH,
            self::FILENAME,
            GasPriceService::GAS_PRICE_FILE_TYPE,
            $year
        );
    }

    /**
     * @param array<mixed> $gasServices
     */
    private function getGasService(GasStationId $gasStationId, SimpleXMLElement $element, array $gasServices): void
    {
        foreach ((array)$element->services->service as $item) {
            if (array_key_exists($gasStationId->getId(), $gasServices)) {
                if (array_key_exists($item, $gasServices[$gasStationId->getId()])) {
                    continue;
                }
            }

            $this->gasServiceService->createGasService(
                $gasStationId,
                $item
            );
        }
    }

    /**
     * @param array<mixed> $gasTypes
     */
    private function getGasPrices(GasStationId $gasStationId, SimpleXMLElement $element, array $gasTypes): void
    {
        foreach ($element->prix as $item) {
            $gasTypeId = (string)$item->attributes()->id;

            if ("" === $gasTypeId) {
                continue;
            }

            $gasTypeId = new GasTypeId($gasTypes[$gasTypeId]['id']);

            $date = (string)$item->attributes()->maj;

            $date = str_replace("T", " ", substr($date, 0, 19));

            if ("" === $date) {
                continue;
            }

            $gasStation = $this->gasStationRepository->findOneBy(['id' => $gasStationId->getId()]);

            if ($gasStation instanceof GasStation) {
                $lastGasPrices = $gasStation->getLastGasPricesDecode();
                if (array_key_exists($gasTypeId->getId(), $lastGasPrices)) {
                    if ($lastGasPrices[$gasTypeId->getId()]->getDate()->format('Y-m-d H:i:s') >= $date) {
                        continue;
                    }
                }
            }

            $this->gasPriceService->createGasPrice($gasStationId, $gasTypeId, $date, (string)$item->attributes()->valeur);
        }
    }
}
