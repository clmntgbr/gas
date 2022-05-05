<?php

namespace App\Api\Controller;

use App\Dto\MapGasStationsDto;
use App\Service\GetMapGasStationsService;
use Symfony\Component\HttpFoundation\Request;

class GetMapGasStations
{
    public static $operationName = 'get_map_gas_stations';

    public function __construct(
        private GetMapGasStationsService $getMapGasStationsService
    )
    {
    }

    public function __invoke(Request $request, $data): MapGasStationsDto
    {
        return $this->getMapGasStationsService->getCollectionData($data);
    }
}