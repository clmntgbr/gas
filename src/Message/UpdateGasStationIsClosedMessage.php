<?php

namespace App\Message;

use App\Common\EntityId\GasStationId;

final class UpdateGasStationIsClosedMessage
{
    public function __construct(
        private GasStationId $gasStationId
    )
    {
    }

    public function getGasStationId(): GasStationId
    {
        return $this->gasStationId;
    }
}
