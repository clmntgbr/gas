<?php

namespace App\Message;

use App\Common\EntityId\GasStationId;

final class CreateGooglePlaceMessage
{
    public function __construct(
        private GasStationId $gasStationId,
        private string       $placeId
    )
    {
    }

    public function getGasStationId(): GasStationId
    {
        return $this->gasStationId;
    }

    public function getPlaceId(): string
    {
        return $this->placeId;
    }
}
