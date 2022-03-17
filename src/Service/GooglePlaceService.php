<?php

namespace App\Service;

use App\Common\EntityId\GasStationId;
use App\Entity\GasStation;
use App\Helper\GasStationStatusHelper;
use App\Lists\GasStationStatusReference;
use App\Message\CreateGooglePlaceAnomalyMessage;
use App\Message\CreateGooglePlaceMessage;
use App\Repository\GasStationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\MessageBusInterface;

final class GooglePlaceService
{
    public function __construct(
        private GasStationRepository   $gasStationRepository,
        private GooglePlaceApiService  $googlePlaceApiService,
        private GasStationStatusHelper $gasStationStatusHelper,
        private MessageBusInterface    $messageBus,
        private EntityManagerInterface $em
    )
    {
    }

    public function update(): void
    {
        $gasStations = $this->gasStationRepository->getGasStationsUpForDetails();

        foreach ($gasStations as $gasStation) {
            $response = $this->googlePlaceApiService->textSearch($gasStation);
            if (null === $response) {
                $this->gasStationStatusHelper->setStatus(GasStationStatusReference::NOT_FOUND_IN_TEXTSEARCH, $gasStation);
                continue;
            }

            $this->messageBus->dispatch(new CreateGooglePlaceMessage(
                new GasStationId($gasStation->getId()),
                $response['place_id']
            ), [new AmqpStamp('async-priority-low', AMQP_NOPARAM, [])]);
        }
    }

    /**
     * @param array<int, GasStation> $gasStations
     */
    public function createAnomalies(GasStation $gasStation, array $gasStations): void
    {
        $gasStationIds = [new GasStationId($gasStation->getId())];
        foreach ($gasStations as $gasStation) {
            $gasStationIds[] = new GasStationId($gasStation->getId());
        }

        $this->messageBus->dispatch(new CreateGooglePlaceAnomalyMessage(
            $gasStationIds
        ), [new AmqpStamp('async-priority-high', AMQP_NOPARAM, [])]);

        $this->em->persist($gasStation);
        $this->em->flush();
    }

    /**
     * @param array<mixed> $details
     */
    public function updateGasStationGooglePlace(GasStation $gasStation, array $details): void
    {
        $googlePlace = $gasStation->getGooglePlace();

        $googlePlace
            ->setGoogleId($details['id'] ?? null)
            ->setPlaceId($details['place_id'] ?? null)
            ->setBusinessStatus($details['business_status'] ?? null)
            ->setIcon($details['icon'] ?? null)
            ->setPhoneNumber($details['international_phone_number'] ?? null)
            ->setCompoundCode($details['plus_code']['compound_code'] ?? null)
            ->setGlobalCode($details['plus_code']['global_code'] ?? null)
            ->setGoogleRating($details['rating'] ?? null)
            ->setRating($details['rating'] ?? null)
            ->setReference($details['reference'] ?? null)
            ->setOpeningHours($details['opening_hours']['weekday_text'] ?? null)
            ->setUserRatingsTotal($details['user_ratings_total'] ?? null)
            ->setUrl($details['url'] ?? null)
            ->setWebsite($details['website'] ?? null);

        $this->em->persist($googlePlace);
    }

    /**
     * @param array<mixed> $details
     */
    public function UpdateGasStationAddressMessage(GasStation $gasStation, array $details): void
    {
        $address = $gasStation->getAddress();

        foreach ($details['address_components'] as $component) {
            foreach ($component['types'] as $type) {
                switch ($type) {
                    case 'street_number':
                        $address->setNumber($component['long_name']);
                        break;
                    case 'route':
                        $address->setStreet($component['long_name']);
                        break;
                    case 'locality':
                        $address->setCity($component['long_name']);
                        break;
                    case 'administrative_area_level_1':
                        $address->setRegion($component['long_name']);
                        break;
                    case 'country':
                        $address->setCountry($component['long_name']);
                        break;
                    case 'postal_code':
                        $address->setPostalCode($component['long_name']);
                        break;
                }
            }
        }

        $address
            ->setVicinity($details['formatted_address'] ?? null)
            ->setLongitude($details['geometry']['location']['lng'] ?? null)
            ->setLatitude($details['geometry']['location']['lat'] ?? null);

        $this->em->persist($address);
    }
}
