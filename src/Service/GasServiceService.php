<?php

namespace App\Service;

use App\Common\EntityId\GasStationId;
use App\Message\CreateGasServiceMessage;
use Symfony\Component\Messenger\MessageBusInterface;

final class GasServiceService
{
    public function __construct(
        private MessageBusInterface $messageBus
    )
    {
    }

    public function createGasService(GasStationId $gasStationId, string $item)
    {
        $this->messageBus->dispatch(new CreateGasServiceMessage(
            $gasStationId,
            $item
        ));
    }
}
