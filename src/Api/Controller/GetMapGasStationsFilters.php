<?php

namespace App\Api\Controller;

use App\Dto\GetMapGasStationsFiltersDto;
use App\Service\GetMapGasStationsFiltersService;
use Symfony\Component\HttpFoundation\Request;

class GetMapGasStationsFilters
{
    public function __construct(
        private GetMapGasStationsFiltersService $getMapGasStationsFiltersService
    )
    {
    }

    public function __invoke(Request $request, $data): GetMapGasStationsFiltersDto
    {
        return $this->getMapGasStationsFiltersService->get();
    }
}