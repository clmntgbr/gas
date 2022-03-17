<?php

namespace App\Api\Controller;

use App\Service\GasStationMapDtoService;
use Symfony\Component\HttpFoundation\Request;

class GetGasStationsMap
{
    public function __construct(
        private GasStationMapDtoService $gasStationMapDtoService
    )
    {
    }

    public function __invoke(Request $request, $data): array
    {
        $gasStationMapDto = $this->gasStationMapDtoService->getCollectionData($data);

        return $this->gasStationMapDtoService->getData($gasStationMapDto);
    }
}