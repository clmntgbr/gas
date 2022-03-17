<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\GasStationStatusHistoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: GasStationStatusHistoryRepository::class)]
#[ApiResource(
    collectionOperations: ['get'],
    itemOperations: ['get']
)]
class GasStationStatusHistory
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\ManyToOne(targetEntity: GasStation::class, inversedBy: 'gasStationStatusHistories', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private GasStation $gasStation;

    #[ORM\ManyToOne(targetEntity: GasStationStatus::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private GasStationStatus $gasStationStatus;

    public function __construct()
    {
        $this->id = rand();
    }

    public function __toString(): string
    {
        return $this->gasStationStatus->getLabel();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getGasStation(): ?GasStation
    {
        return $this->gasStation;
    }

    public function setGasStation(?GasStation $gasStation): self
    {
        $this->gasStation = $gasStation;

        return $this;
    }

    public function getGasStationStatus(): ?GasStationStatus
    {
        return $this->gasStationStatus;
    }

    public function setGasStationStatus(?GasStationStatus $gasStationStatus): self
    {
        $this->gasStationStatus = $gasStationStatus;

        return $this;
    }
}
