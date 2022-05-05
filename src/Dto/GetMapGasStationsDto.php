<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class GetMapGasStationsDto
{
    #[NotNull(), NotBlank]
    public string $longitude;

    #[NotNull(), NotBlank]
    public string $latitude;

    #[NotNull(), NotBlank]
    public string $radius;

    public array $filters = [];
}