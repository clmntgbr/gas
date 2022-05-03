<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class GasStationMapCoordinateDto
{
    #[Assert\NotNull()]
    #[Assert\NotBlank()]
    public string $longitude;

    #[Assert\NotNull()]
    #[Assert\NotBlank()]
    public string $latitude;

    #[Assert\NotNull()]
    #[Assert\NotBlank()]
    public string $radius;

    public array $filters;
}