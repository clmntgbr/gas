<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class GasStationMapDto
{
    /**
     * @var GasStationMapDataDto
     * @Assert\Valid()
     */
    public array $gasStationMapData;

    public array $lowGasPrices;
}