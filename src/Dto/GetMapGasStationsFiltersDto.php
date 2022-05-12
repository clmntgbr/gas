<?php

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;

class GetMapGasStationsFiltersDto
{
    #[Groups(["read"])]
    public array $departments;

    #[Groups(["read"])]
    public array $postalCodes;

    #[Groups(["read"])]
    public array $cities;

    #[Groups(["read"])]
    public array $gasTypes;

    #[Groups(["read"])]
    public array $services;
}