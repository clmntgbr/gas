<?php

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class GasStationMapDataDto
{
    #[Assert\NotNull()]
    #[Assert\NotBlank()]
    #[Groups(["read"])]
    public string $id;

    #[Assert\NotNull()]
    #[Assert\NotBlank()]
    #[Groups(["read"])]
    public string $name;

    #[Assert\NotNull()]
    #[Assert\NotBlank()]
    #[Groups(["read"])]
    public string $company;

    #[Assert\NotNull()]
    #[Assert\NotBlank()]
    #[Groups(["read"])]
    public string $googlePlaceId;

    #[Assert\NotNull()]
    #[Assert\NotBlank()]
    #[Groups(["read"])]
    public string $adressId;

    #[Assert\NotNull()]
    #[Assert\NotBlank()]
    #[Groups(["read"])]
    public string $vicinity;

    #[Assert\NotNull()]
    #[Assert\NotBlank()]
    #[Groups(["read"])]
    public string $longitude;

    #[Assert\NotNull()]
    #[Assert\NotBlank()]
    #[Groups(["read"])]
    public string $latitude;

    #[Assert\NotNull()]
    #[Assert\NotBlank()]
    #[Groups(["read"])]
    public string $distance;

    #[Groups(["read"])]
    public ?string $url;

    #[Groups(["read"])]
    public array $lastGasPrices;

    #[Groups(["read"])]
    public array $previousGasPrices;
}