<?php

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class MapGasStationsDataDto
{
    #[NotNull(), NotBlank, Groups(["read"])]
    public string $id;

    #[NotNull(), NotBlank, Groups(["read"])]
    public string $name;

    #[NotNull(), NotBlank, Groups(["read"])]
    public string $company;

    #[NotNull(), NotBlank, Groups(["read"])]
    public string $vicinity;

    #[NotNull(), NotBlank, Groups(["read"])]
    public string $longitude;

    #[NotNull(), NotBlank, Groups(["read"])]
    public string $latitude;

    #[NotNull(), NotBlank, Groups(["read"])]
    public string $previewName;

    #[Groups(["read"])]
    public ?string $previewPath;

    #[NotNull(), NotBlank, Groups(["read"])]
    public string $gasStationStatus;

    #[NotNull(), NotBlank, Groups(["read"])]
    public string $gasServices;

    #[Groups(["read"])]
    public ?string $url;

    #[Groups(["read"])]
    public array $lastGasPrices;

    #[Groups(["read"])]
    public array $previousGasPrices;
}