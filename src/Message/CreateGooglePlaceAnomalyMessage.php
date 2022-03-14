<?php

namespace App\Message;

use App\Common\EntityId\GasStationId;

final class CreateGooglePlaceAnomalyMessage
{
    /** @param GasStationId[] $gasStationIds */
    public function __construct(private $gasStationIds)
    {
    }

    /** @var GasStationId[] */
    public function getGasStationIds()
    {
        return $this->gasStationIds;
    }
}
