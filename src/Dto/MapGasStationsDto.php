<?php

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Valid;

class MapGasStationsDto
{
    #[Valid(), Groups(["read"])]
    /** @var MapGasStationsDataDto[] */
    public $mapGasStationsDataDto;

    #[Groups(["read"])]
    public array $lowGasPrices;

    public function addMapGasStationsDataDto(MapGasStationsDataDto $mapGasStationsDataDto): void
    {
        $this->mapGasStationsDataDto[] = $mapGasStationsDataDto;
    }
}