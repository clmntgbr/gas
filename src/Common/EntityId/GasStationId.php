<?php

namespace App\Common\EntityId;

final class GasStationId
{
    /** @var int */
    private $id;

    public function __construct($id)
    {
        $this->id = (int)$id;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
