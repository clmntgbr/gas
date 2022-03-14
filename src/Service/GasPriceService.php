<?php

namespace App\Service;

use App\Common\EntityId\GasStationId;
use App\Common\EntityId\GasTypeId;
use App\Entity\GasPrice;
use App\Entity\GasStation;
use App\Message\CreateGasPriceMessage;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\MessageBusInterface;

final class GasPriceService
{
    const GAS_PRICE_FILE_TYPE = "xml";
    const GAS_PRICE_INSTANT_URL = 'GAS_PRICE_INSTANT_URL';

    public function __construct(
        private DotEnvService       $dotEnvService,
        private MessageBusInterface $messageBus
    )
    {
    }

    /**
     * @throws \App\Common\Exception\DotEnvException
     */
    public function downloadGasPriceFile(string $path, string $name, string $type): string
    {
        FileSystemService::delete($path, $name);

        FileSystemService::download($this->dotEnvService->findByParameter(self::GAS_PRICE_INSTANT_URL), $name, $path);

        if (false === FileSystemService::exist($path, $name)) {
            throw new \Exception();
        }

        if (false === FileSystemService::unzip(sprintf("%s%s", $path, $name), $path)) {
            throw new \Exception();
        }

        FileSystemService::delete($path, $name);

        if (false === $xmlPath = FileSystemService::find($path, "%\.($type)$%i")) {
            throw new \Exception();
        }

        return $xmlPath;
    }

    public function createGasPrice(GasStationId $gasStationId, GasTypeId $gasTypeId, string $date, string $value)
    {
        $this->messageBus->dispatch(new CreateGasPriceMessage(
            $gasStationId,
            $gasTypeId,
            $date,
            $value
        ), [new AmqpStamp('async-priority-low', AMQP_NOPARAM, [])]);
    }

    public function updateLastGasPrices(GasStation $gasStation, GasPrice $gasPrice)
    {
        $lastGasPrices = $gasStation->getLastGasPrices();

        if (!array_key_exists($gasPrice->getGasType()->getId(), $lastGasPrices)) {
            $gasStation->setLastGasPrices($gasPrice->getGasType(), $gasPrice);
            return;
        }

        if ($lastGasPrices[$gasPrice->getGasType()->getId()]['datetimestamp'] <= $gasPrice->getDateTimestamp()) {
            $gasStation->setLastGasPrices($gasPrice->getGasType(), $gasPrice);
        }
    }
}
