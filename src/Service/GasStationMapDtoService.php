<?php

namespace App\Service;

use App\Dto\GasStationMapCoordinateDto;
use App\Dto\GasStationMapDataDto;
use App\Dto\GasStationMapDto;
use App\Repository\GasStationRepository;
use Safe;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GasStationMapDtoService
{
    private array $lowGasPrices = [];

    public function __construct(
        private ValidatorInterface   $validator,
        private GasStationRepository $gasStationRepository
    )
    {
    }

    public function getData(GasStationMapCoordinateDto $mapGasStationsDto)
    {
        $gasStationsForMapData = $this->gasStationRepository->getGasStationsForMap(
            $mapGasStationsDto->longitude,
            $mapGasStationsDto->latitude,
            $mapGasStationsDto->radius
        );

        return $this->hydrate($gasStationsForMapData);
    }

    private function hydrate(array $gasStationsForMapData)
    {
        $gasStationsForMapDto = new GasStationMapDto();

        foreach ($gasStationsForMapData as $gasStationsForMapDatum) {
            $gasStationsForMapDatumDto = $this->transformer($gasStationsForMapDatum);
            $gasStationsForMapDto->gasStationMapData[] = $gasStationsForMapDatumDto;
        }

        $gasStationsForMapDto->lowGasPrices = $this->lowGasPrices;

        $this->validateDto($gasStationsForMapDto);

        return $gasStationsForMapDto;
    }

    private function transformer(array $gasStationsForMapDatum)
    {
        $gasStationsForMapDto = new GasStationMapDataDto();
        $gasStationsForMapDto->id = $gasStationsForMapDatum['gas_station_id'];
        $gasStationsForMapDto->name = $gasStationsForMapDatum['gas_station_name'];
        $gasStationsForMapDto->company = $gasStationsForMapDatum['company'];
        $gasStationsForMapDto->adressId = $gasStationsForMapDatum['address_id'];
        $gasStationsForMapDto->distance = $gasStationsForMapDatum['distance'];
        $gasStationsForMapDto->googlePlaceId = $gasStationsForMapDatum['google_place_id'];
        $gasStationsForMapDto->latitude = $gasStationsForMapDatum['latitude'];
        $gasStationsForMapDto->longitude = $gasStationsForMapDatum['longitude'];
        $gasStationsForMapDto->vicinity = $gasStationsForMapDatum['vicinity'];
        $gasStationsForMapDto->url = $gasStationsForMapDatum['url'];

        $gasStationsForMapDto = $this->getLowGasPrices($gasStationsForMapDatum, $gasStationsForMapDto);
        $gasStationsForMapDto = $this->getLastGasPrices($gasStationsForMapDatum, $gasStationsForMapDto);
        $gasStationsForMapDto = $this->getPreviousGasPrices($gasStationsForMapDatum, $gasStationsForMapDto);

        return $gasStationsForMapDto;
    }

    private function getLowGasPrices(array $gasStationsForMapDatum, GasStationMapDataDto $gasStationsForMapDto): GasStationMapDataDto
    {
        $lastGasPrices = Safe\json_decode($gasStationsForMapDatum['last_gas_prices'], true);

        foreach ($lastGasPrices as $key => $gasPrice) {
            if (!array_key_exists($key, $this->lowGasPrices)) {
                $this->updateLowGasPrices($gasStationsForMapDto, $key, $gasPrice);
                continue;
            }

            if ($gasPrice['gasPriceValue'] <= $this->lowGasPrices[$key]['gasPriceValue']) {
                $this->updateLowGasPrices($gasStationsForMapDto, $key, $gasPrice);
            }
        }

        return $gasStationsForMapDto;
    }

    private function getLastGasPrices(array $gasStationsForMapDatum, GasStationMapDataDto $gasStationsForMapDto): GasStationMapDataDto
    {
        $lastGasPrices = Safe\json_decode($gasStationsForMapDatum['last_gas_prices'], true);

        foreach ($lastGasPrices as $gasPrice) {
            $gasStationsForMapDto->lastGasPrices[$gasPrice['gasTypeId']] = $this->format($gasPrice, $gasStationsForMapDto);
        }

        return $gasStationsForMapDto;
    }

    private function getPreviousGasPrices(array $gasStationsForMapDatum, GasStationMapDataDto $gasStationsForMapDto): GasStationMapDataDto
    {
        $previousGasPrices = Safe\json_decode($gasStationsForMapDatum['previous_gas_prices'], true);

        foreach ($previousGasPrices as $gasPrice) {
            $gasStationsForMapDto->previousGasPrices[$gasPrice['gasTypeId']] = $this->format($gasPrice, $gasStationsForMapDto);
        }

        return $gasStationsForMapDto;
    }

    private function format(array $gasPrice, GasStationMapDataDto $gasStationsForMapDto)
    {
        return [
            'gasTypeId' => $gasPrice['gasTypeId'],
            'gasPriceValue' => $gasPrice['gasPriceValue'],
            'gasStationId' => $gasStationsForMapDto->id,
            'gasPriceId' => $gasPrice['id'],
            'datetimestamp' => $gasPrice['datetimestamp'],
            'gasTypeLabel' => $gasPrice['gasTypeLabel'],
        ];
    }

    private function updateLowGasPrices(GasStationMapDataDto $gasStationsForMapDto, string $key, array $gasPrice)
    {
        $this->lowGasPrices[$key] = [
            'gasTypeId' => $key,
            'gasPriceValue' => $gasPrice['gasPriceValue'],
            'gasStationId' => $gasStationsForMapDto->id,
            'gasPriceId' => $gasPrice['id'],
        ];
    }

    public function getCollectionData(array $data): GasStationMapCoordinateDto
    {
        $gasStationMapCoordinateDto = new GasStationMapCoordinateDto();
        $gasStationMapCoordinateDto->longitude = $data['longitude'] ?? null;
        $gasStationMapCoordinateDto->latitude = $data['latitude'] ?? null;
        $gasStationMapCoordinateDto->radius = $data['radius'] ?? null;

        $this->validateDto($gasStationMapCoordinateDto);

        return $gasStationMapCoordinateDto;
    }

    public function validateDto($gasStationMapCoordinateDto)
    {
        $errors = $this->validator->validate($gasStationMapCoordinateDto);

        if (count($errors) > 0) {
            throw new \Exception(sprintf('GasStationMapDataDto errors : %s', (string)$errors));
        }
    }
}