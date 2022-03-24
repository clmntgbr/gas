<?php

namespace App\Service;

use App\Common\EntityId\GasStationId;
use App\Common\EntityId\GasTypeId;
use App\Entity\GasPrice;
use App\Entity\GasStation;
use App\Message\CreateGasPriceMessage;
use App\Repository\GasPriceRepository;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\MessageBusInterface;

final class GasPriceService
{
    const GAS_PRICE_FILE_TYPE = "xml";
    const GAS_PRICE_INSTANT_URL = 'GAS_PRICE_INSTANT_URL';
    const GAS_PRICE_YEAR_URL = 'GAS_PRICE_YEAR_URL';

    public function __construct(
        private DotEnvService       $dotEnvService,
        private MessageBusInterface $messageBus,
        private GasPriceRepository  $gasPriceRepository
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

        if (null === $xmlPath = FileSystemService::find($path, "%\.($type)$%i")) {
            throw new \Exception();
        }

        return $xmlPath;
    }

    /**
     * @throws \App\Common\Exception\DotEnvException
     */
    public function downloadGasPriceYearFile(string $path, string $name, string $type, string $year): string
    {
        FileSystemService::delete($path, $name);

        FileSystemService::download(sprintf($this->dotEnvService->findByParameter(self::GAS_PRICE_YEAR_URL), $year), $name, $path);

        if (false === FileSystemService::exist($path, $name)) {
            throw new \Exception();
        }

        if (false === FileSystemService::unzip(sprintf("%s%s", $path, $name), $path)) {
            throw new \Exception();
        }

        FileSystemService::delete($path, $name);

        if (null === $xmlPath = FileSystemService::find($path, "%\.($type)$%i")) {
            throw new \Exception();
        }

        return $xmlPath;
    }

    public function createGasPrice(GasStationId $gasStationId, GasTypeId $gasTypeId, string $date, string $value): void
    {
        $this->messageBus->dispatch(new CreateGasPriceMessage(
            $gasStationId,
            $gasTypeId,
            $date,
            $value
        ), [new AmqpStamp('async-priority-low', AMQP_NOPARAM, [])]);
    }

    /**
     * @throws \Doctrine\ORM\Query\QueryException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function updatePreviousGasPrices(GasStation $gasStation): void
    {
        $lastGasPrices = $gasStation->getLastGasPricesDecode();

        foreach ($lastGasPrices as $lastGasPrice) {
            $gasPrice = $this->gasPriceRepository->findLastGasPriceByTypeAndGasStationExceptId($gasStation, $lastGasPrice->getGasType(), $lastGasPrice->getId());
            if (null === $gasPrice) {
                continue;
            }
            $gasStation->setPreviousGasPrices($gasPrice->getGasType(), $gasPrice);
        }
    }

    public function updateLastGasPrices(GasStation $gasStation, GasPrice $gasPrice): void
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
