<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Api\Controller\GetMapGasStations;
use App\Api\Controller\GetMapGasStationsFilters;
use App\Repository\GasStationRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: GasStationRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get',
        'get_map_gas_stations' => [
            'method' => 'GET',
            'path' => '/map/gas_stations',
            'controller' => GetMapGasStations::class,
            'pagination_enabled' => false
        ],
        'get_map_gas_stations_filters' => [
            'method' => 'GET',
            'path' => '/map/gas_stations/filters',
            'controller' => GetMapGasStationsFilters::class,
            'pagination_enabled' => false
        ],
    ],
    itemOperations: ['get'],
    normalizationContext: ['groups' => ['read']]
)]
class GasStation
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ORM\Column(type: Types::STRING)]
    #[Groups(["read"])]
    private string $id;

    #[ORM\Column(type: Types::STRING, length: 10)]
    #[Groups(["read"])]
    private string $pop;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(["read"])]
    private ?string $name = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    #[Groups(["read"])]
    private ?string $company = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Groups(["read"])]
    private ?DateTimeImmutable $closedAt = null;

    #[ORM\ManyToOne(targetEntity: GasStationStatus::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["read"])]
    private GasStationStatus $gasStationStatus;

    #[ORM\OneToOne(targetEntity: Address::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["read"])]
    private Address $address;

    #[ORM\ManyToOne(targetEntity: Media::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["read"])]
    private Media $preview;

    #[ORM\ManyToOne(targetEntity: GooglePlace::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["read"])]
    private GooglePlace $googlePlace;

    #[ORM\Column(type: Types::ARRAY)]
    private array $element = [];

    #[ORM\ManyToMany(targetEntity: GasService::class, mappedBy: 'gasStations', cascade: ['persist'])]
    #[Groups(["read"])]
    private Collection $gasServices;

    #[ORM\OneToMany(mappedBy: 'gasStation', targetEntity: GasPrice::class)]
    private Collection $gasPrices;

    #[ORM\Column(type: Types::JSON)]
    private array $lastGasPrices = [];

    #[ORM\Column(type: Types::JSON)]
    private array $previousGasPrices = [];

    private array $lastGasPricesDecode = [];

    private array $previousGasPricesDecode = [];

    #[ORM\OneToMany(mappedBy: 'gasStation', targetEntity: GasStationStatusHistory::class)]
    private Collection $gasStationStatusHistories;

    public function __construct()
    {
        $this->gasPrices = new ArrayCollection();
        $this->gasServices = new ArrayCollection();
        $this->gasStationStatusHistories = new ArrayCollection();
        $this->lastGasPrices = [];
        $this->previousGasPrices = [];
        $this->lastGasPricesDecode = [];
        $this->previousGasPricesDecode = [];
    }

    public function __toString(): string
    {
        return $this->id;
    }

    public function getPop(): ?string
    {
        return $this->pop;
    }

    public function setPop(string $pop): self
    {
        $this->pop = $pop;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getClosedAt(): ?DateTimeImmutable
    {
        return $this->closedAt;
    }

    public function setClosedAt(?DateTimeImmutable $closedAt): self
    {
        $this->closedAt = $closedAt;

        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * @param array<mixed> $element
     */
    public function setElement(array $element): self
    {
        $this->element = $element;

        return $this;
    }

    public function getGasStationStatus(): ?GasStationStatus
    {
        return $this->gasStationStatus;
    }

    public function setGasStationStatus(GasStationStatus $gasStationStatus): self
    {
        $this->gasStationStatus = $gasStationStatus;

        return $this;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function setAddress(Address $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPreview(): ?Media
    {
        return $this->preview;
    }

    public function setPreview(Media $preview): self
    {
        $this->preview = $preview;

        return $this;
    }

    public function getGooglePlace(): GooglePlace
    {
        return $this->googlePlace;
    }

    public function setGooglePlace(GooglePlace $googlePlace): self
    {
        $this->googlePlace = $googlePlace;

        return $this;
    }

    /**
     * @return Collection<int, GasPrice>
     */
    public function getGasPrices(): Collection
    {
        return $this->gasPrices;
    }

    public function addGasPrice(GasPrice $gasPrice): self
    {
        if (!$this->gasPrices->contains($gasPrice)) {
            $this->gasPrices[] = $gasPrice;
            $gasPrice->setGasStation($this);
        }

        return $this;
    }

    public function removeGasPrice(GasPrice $gasPrice): self
    {
        $this->gasPrices->removeElement($gasPrice);

        return $this;
    }

    /**
     * @return Collection<int, GasService>
     */
    public function getGasServices(): Collection
    {
        return $this->gasServices;
    }

    public function addGasService(GasService $gasService): self
    {
        if (!$this->gasServices->contains($gasService)) {
            $this->gasServices[] = $gasService;
            $gasService->addGasStation($this);
        }

        return $this;
    }

    public function removeGasService(GasService $gasService): self
    {
        if ($this->gasServices->removeElement($gasService)) {
            $gasService->removeGasStation($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, GasStationStatusHistory>
     */
    public function getGasStationStatusHistories(): Collection
    {
        return $this->gasStationStatusHistories;
    }

    public function addGasStationStatusHistory(GasStationStatusHistory $gasStationStatusHistory): self
    {
        if (!$this->gasStationStatusHistories->contains($gasStationStatusHistory)) {
            $this->gasStationStatusHistories[] = $gasStationStatusHistory;
            $gasStationStatusHistory->setGasStation($this);
        }

        return $this;
    }

    public function removeGasStationStatusHistory(GasStationStatusHistory $gasStationStatusHistory): self
    {
        $this->gasStationStatusHistories->removeElement($gasStationStatusHistory);

        return $this;
    }

    public function hasGasService(GasService $gasService): bool
    {
        return $this->gasServices->contains($gasService);
    }

    public function getPreviousGasStationStatusHistory(): ?GasStationStatusHistory
    {
        $lastGasStationStatusHistory = $this->gasStationStatusHistories->last();
        $previousGasStationStatusHistory = null;

        foreach ($this->gasStationStatusHistories as $gasStationStatusHistory) {
            if ($lastGasStationStatusHistory !== false && $gasStationStatusHistory->getId() !== $lastGasStationStatusHistory->getId()) {
                $previousGasStationStatusHistory = $gasStationStatusHistory;
            }
        }

        if ($lastGasStationStatusHistory !== false && null === $previousGasStationStatusHistory) {
            return $lastGasStationStatusHistory;
        }

        return $previousGasStationStatusHistory;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getLastGasStationStatusHistory(): ?GasStationStatusHistory
    {
        return $this->gasStationStatusHistories->last() ?? null;
    }

    /**
     * @return array<mixed>
     */
    public function getLastGasPrices()
    {
        return $this->lastGasPrices;
    }

    public function setLastGasPrices(GasType $gasType, GasPrice $gasPrice): self
    {
        $this->lastGasPrices[$gasType->getId()] = $this->hydrateGasPrices($gasPrice);
        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function getPreviousGasPrices()
    {
        return $this->previousGasPrices;
    }

    public function setPreviousGasPrices(GasType $gasType, GasPrice $gasPrice): self
    {
        $this->previousGasPrices[$gasType->getId()] = $this->hydrateGasPrices($gasPrice);
        return $this;
    }

    private function hydrateGasPrices(GasPrice $gasPrice)
    {
        return [
            'id' => $gasPrice->getId(),
            'datetimestamp' => $gasPrice->getDateTimestamp(),
            'gasPriceValue' => $gasPrice->getValue(),
            'gasTypeId' => $gasPrice->getGasType()->getId(),
            'gasTypeLabel' => $gasPrice->getGasType()->getLabel(),
        ];
    }

    /**
     * @return GasPrice[]
     */
    public function getLastGasPricesDecode()
    {
        return $this->lastGasPricesDecode;
    }

    public function setLastGasPricesDecode(GasType $gasType, GasPrice $gasPrice): self
    {
        $this->lastGasPricesDecode[$gasType->getId()] = $gasPrice;

        return $this;
    }

    /**
     * @return GasPrice[]
     */
    public function getPreviousGasPricesDecode()
    {
        return $this->previousGasPricesDecode;
    }

    public function setPreviousGasPricesDecode(GasType $gasType, GasPrice $gasPrice): self
    {
        $this->previousGasPricesDecode[$gasType->getId()] = $gasPrice;

        return $this;
    }
}
