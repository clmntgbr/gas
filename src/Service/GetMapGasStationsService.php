<?php

namespace App\Service;

use App\Dto\GetMapGasStationsDto;
use App\Dto\MapGasStationsDataDto;
use App\Dto\MapGasStationsDto;
use App\Repository\GasStationRepository;
use Safe;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GetMapGasStationsService
{
    public function __construct(
        private ValidatorInterface $validator,
        private GetMapGasStationsDto $getMapGasStationsDto,
        private GasStationRepository $gasStationRepository,
        private array $lowGasPrices = []
    )
    {
    }

    public function getCollectionData(array $data): MapGasStationsDto
    {
        $this->getMapGasStationsDto->longitude = $data['longitude'] ?? null;
        $this->getMapGasStationsDto->latitude = $data['latitude'] ?? null;
        $this->getMapGasStationsDto->radius = $data['radius'] ?? null;
        $this->getMapGasStationsDto->filters = Safe\json_decode($data['filters'], true) ?? null;

        $this->validate($this->getMapGasStationsDto);

        $getMapGasStationsData = $this->gasStationRepository->getGasStationsForMap(
            $this->getMapGasStationsDto->longitude,
            $this->getMapGasStationsDto->latitude,
            $this->getMapGasStationsDto->radius,
            $this->getMapGasStationsDto->filters
        );

        return $this->hydrate($getMapGasStationsData);
    }

    private function validate($entity)
    {
        $errors = $this->validator->validate($entity);

        if (count($errors) > 0) {
            throw new \Exception(sprintf('%s errors : %s', get_class($entity), $errors));
        }
    }

    private function hydrate(array $data): MapGasStationsDto
    {
        $mapGasStationsDto = new MapGasStationsDto();

        foreach ($data as $datum) {
            $mapGasStationsDataDto = $this->transformer($datum);
            $mapGasStationsDto->addMapGasStationsDataDto($mapGasStationsDataDto);
        }

        $mapGasStationsDto->lowGasPrices = $this->lowGasPrices;

        $this->validate($mapGasStationsDto);

        return $mapGasStationsDto;
    }

    private function transformer(array $datum)
    {
        $mapGasStationsDataDto = new MapGasStationsDataDto();
        $mapGasStationsDataDto->id = $datum['gas_station_id'];
        $mapGasStationsDataDto->name = $datum['gas_station_name'];
        $mapGasStationsDataDto->company = $datum['company'];
        $mapGasStationsDataDto->gasStationStatus = $datum['gas_station_status'];
        $mapGasStationsDataDto->gasServices = $datum['gas_services'];
        $mapGasStationsDataDto->latitude = $datum['latitude'];
        $mapGasStationsDataDto->longitude = $datum['longitude'];
        $mapGasStationsDataDto->previewName = $datum['preview_name'];
        $mapGasStationsDataDto->previewPath = $datum['preview_path'];
        $mapGasStationsDataDto->vicinity = $datum['vicinity'];
        $mapGasStationsDataDto->url = $datum['url'];

        $mapGasStationsDataDto = $this->getLowGasPrices($datum, $mapGasStationsDataDto);
        $mapGasStationsDataDto = $this->getLastGasPrices($datum, $mapGasStationsDataDto);
        $mapGasStationsDataDto = $this->getPreviousGasPrices($datum, $mapGasStationsDataDto);

        return $mapGasStationsDataDto;
    }

    private function getLowGasPrices(array $gasStationsForMapDatum, MapGasStationsDataDto $mapGasStationsDataDto): MapGasStationsDataDto
    {
        $lastGasPrices = Safe\json_decode($gasStationsForMapDatum['last_gas_prices'], true);

        foreach ($lastGasPrices as $key => $gasPrice) {
            if (!array_key_exists($key, $this->lowGasPrices)) {
                $this->updateLowGasPrices($mapGasStationsDataDto, $key, $gasPrice);
                continue;
            }

            if ($gasPrice['gasPriceValue'] <= $this->lowGasPrices[$key]['gasPriceValue']) {
                $this->updateLowGasPrices($mapGasStationsDataDto, $key, $gasPrice);
            }
        }

        return $mapGasStationsDataDto;
    }

    private function updateLowGasPrices(MapGasStationsDataDto $mapGasStationsDataDto, string $key, array $gasPrice)
    {
        $this->lowGasPrices[$key] = $this->format($gasPrice, $mapGasStationsDataDto);
    }

    private function format(array $gasPrice, MapGasStationsDataDto $mapGasStationsDataDto)
    {
        return [
            'gasTypeId' => $gasPrice['gasTypeId'],
            'gasPriceValue' => $gasPrice['gasPriceValue'],
            'gasStationId' => $mapGasStationsDataDto->id,
            'gasPriceId' => $gasPrice['id'],
            'datetimestamp' => $gasPrice['datetimestamp'],
            'gasTypeLabel' => $gasPrice['gasTypeLabel'],
        ];
    }

    private function getLastGasPrices(array $gasStationsForMapDatum, MapGasStationsDataDto $mapGasStationsDataDto): MapGasStationsDataDto
    {
        $lastGasPrices = Safe\json_decode($gasStationsForMapDatum['last_gas_prices'], true);

        foreach ($lastGasPrices as $gasPrice) {
            $mapGasStationsDataDto->lastGasPrices[$gasPrice['gasTypeId']] = $this->format($gasPrice, $mapGasStationsDataDto);
        }

        return $mapGasStationsDataDto;
    }

    private function getPreviousGasPrices(array $gasStationsForMapDatum, MapGasStationsDataDto $mapGasStationsDataDto): MapGasStationsDataDto
    {
        $previousGasPrices = Safe\json_decode($gasStationsForMapDatum['previous_gas_prices'], true);

        foreach ($previousGasPrices as $gasPrice) {
            $mapGasStationsDataDto->previousGasPrices[$gasPrice['gasTypeId']] = $this->format($gasPrice, $mapGasStationsDataDto);
        }

        return $mapGasStationsDataDto;
    }
}