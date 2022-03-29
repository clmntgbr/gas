<?php

namespace App\Api\Controller;

use App\Dto\GasStationMapDto;
use App\Service\GasStationMapDtoService;
use Symfony\Component\HttpFoundation\Request;

class GetGasStationsMap
{
    public static $operationName = 'get_gas_stations_map';

    public function __construct(
        private GasStationMapDtoService $gasStationMapDtoService
    )
    {
    }

    public function __invoke(Request $request, $data): GasStationMapDto
    {
        $gasStationMapCoordinateDto = $this->gasStationMapDtoService->getCollectionData($data);

        return $this->gasStationMapDtoService->getData($gasStationMapCoordinateDto);
    }
}