<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class GasStationMapCoordinateDto
{
    /**
     * @var string
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    public $longitude;

    /**
     * @var string
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    public $latitude;

    /**
     * @var string
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    public $radius;

    /**
     * @var array
     */
    public $filters;
}