<?php

namespace App\MessageHandler;

use App\Entity\GasPrice;
use App\Helper\GasStationStatusHelper;
use App\Lists\CurrencyReference;
use App\Lists\GasStationStatusReference;
use App\Message\CreateGasPriceMessage;
use App\Repository\CurrencyRepository;
use App\Repository\GasStationRepository;
use App\Repository\GasTypeRepository;
use App\Service\GasPriceService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Safe\DateTimeImmutable;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class CreateGasPriceMessageHandler implements MessageHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private GasStationStatusHelper $gasStationStatusHelper,
        private GasPriceService        $gasPriceService,
        private GasStationRepository   $gasStationRepository,
        private GasTypeRepository      $gasTypeRepository,
        private CurrencyRepository     $currencyRepository
    )
    {
    }

    public function __invoke(CreateGasPriceMessage $message): void
    {
        if (!$this->em->isOpen()) {
            $this->em = EntityManager::create($this->em->getConnection(), $this->em->getConfiguration());
        }

        $gasStation = $this->gasStationRepository->findOneBy(['id' => $message->getGasStationId()->getId()]);

        if (null === $gasStation) {
            throw new UnrecoverableMessageHandlingException(sprintf('Gas Station is null (id: %s)', $message->getGasStationId()->getId()));
        }

        $gasType = $this->gasTypeRepository->findOneBy(['id' => $message->getGasTypeId()->getId()]);

        if (null === $gasType) {
            throw new UnrecoverableMessageHandlingException(sprintf('Gas Type is null (id: %s)', $message->getGasTypeId()->getId()));
        }

        $currency = $this->currencyRepository->findOneBy(['reference' => CurrencyReference::EUR]);

        if (null === $currency) {
            throw new UnrecoverableMessageHandlingException('Currency is null (reference: eur)');
        }

        $gasPrice = new GasPrice();
        $gasPrice
            ->setCurrency($currency)
            ->setGasType($gasType)
            ->setGasStation($gasStation)
            ->setDate(DateTimeImmutable::createFromFormat('Y-m-d H:i:s', str_replace("T", " ", substr($message->getDate(), 0, 19))))
            ->setDateTimestamp($gasPrice->getDate()->getTimestamp())
            ->setValue((int)str_replace([',', '.'], '', $message->getValue()));

        $this->em->persist($gasPrice);
        $this->em->flush();

        $this->gasPriceService->updateLastGasPrices($gasStation, $gasPrice);

        if (GasStationStatusReference::CLOSED === $gasStation->getGasStationStatus()->getReference()) {
            $this->gasStationStatusHelper->setStatus($gasStation->getPreviousGasStationStatusHistory()->getGasStationStatus()->getReference(), $gasStation);
        }

        $this->em->persist($gasStation);
        $this->em->flush();
    }
}
