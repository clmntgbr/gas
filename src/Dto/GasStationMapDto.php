<?php

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class GasStationMapDto
{
    #[Assert\Valid()]
    #[Groups(["read"])]
    /** @var GasStationMapDto[] */
    public $gasStationMapData;

    #[Groups(["read"])]
    public array $lowGasPrices;
}