<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class GasStationMapDataDto
{
    /**
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    public string $id;

    /**
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    public string $name;

    /**
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    public string $company;

    /**
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    public string $googlePlaceId;

    /**
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    public string $adressId;

    /**
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    public string $vicinity;

    /**
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    public string $longitude;

    /**
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    public string $latitude;

    /**
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    public string $distance;

    public ?string $url;

    public array $lastGasPrices;
}