<?php

namespace App\Service;

use App\Dto\GasStationMapDto;
use App\Repository\GasStationRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GasStationMapDtoService
{
    public function __construct(
        private ValidatorInterface   $validator,
        private GasStationRepository $gasStationRepository
    )
    {
    }

    public function getData(GasStationMapDto $mapGasStationsDto)
    {
        return $this->gasStationRepository->getGasStationsForMap(
            $mapGasStationsDto->longitude,
            $mapGasStationsDto->latitude,
            $mapGasStationsDto->radius
        );
    }

    public function getCollectionData(array $data): GasStationMapDto
    {
        $gasStationMapDto = new GasStationMapDto();
        $gasStationMapDto->longitude = $data['longitude'] ?? null;
        $gasStationMapDto->latitude = $data['latitude'] ?? null;
        $gasStationMapDto->radius = $data['radius'] ?? null;

        $this->validateDto($gasStationMapDto);

        return $gasStationMapDto;
    }

    public function validateDto(GasStationMapDto $gasStationMapDto)
    {
        $errors = $this->validator->validate($gasStationMapDto);

        if (count($errors) > 0) {
            throw new \Exception(sprintf('GasStationMapDto errors : %s', (string)$errors));
        }
    }
}