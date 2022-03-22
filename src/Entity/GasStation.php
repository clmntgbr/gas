<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Api\Controller\GetGasStationsMap;
use App\Repository\GasStationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: GasStationRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get',
        'get_gas_stations_map' => [
            'method' => 'GET',
            'path' => '/map/gas_stations',
            'controller' => GetGasStationsMap::class,
            'pagination_enabled' => false
        ],
    ],
    itemOperations: ['get']
)]
class GasStation
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ORM\Column(type: Types::STRING)]
    private string $id;

    #[ORM\Column(type: Types::STRING, length: 10)]
    private string $pop;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $company = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $closedAt = null;

    #[ORM\ManyToOne(targetEntity: GasStationStatus::class)]
    #[ORM\JoinColumn(nullable: false)]
    private GasStationStatus $gasStationStatus;

    #[ORM\OneToOne(targetEntity: Address::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private Address $address;

    #[ORM\ManyToOne(targetEntity: Media::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private Media $preview;

    #[ORM\ManyToOne(targetEntity: GooglePlace::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private GooglePlace $googlePlace;

    /** @var array<mixed> $element */
    #[ORM\Column(type: Types::ARRAY)]
    private $element = [];

    /** @var Collection<int, GasService> */
    #[ORM\ManyToMany(targetEntity: GasService::class, mappedBy: 'gasStations', cascade: ['persist'])]
    private $gasServices;

    /** @var Collection<int, GasPrice> */
    #[ORM\OneToMany(mappedBy: 'gasStation', targetEntity: GasPrice::class)]
    private $gasPrices;

    /** @var array<mixed> $lastGasPrices */
    #[ORM\Column(type: Types::JSON)]
    private $lastGasPrices = [];

    /** @var array<mixed> $lastGasPricesDecode */
    private $lastGasPricesDecode = [];

    /** @var Collection<int, GasStationStatusHistory> */
    #[ORM\OneToMany(mappedBy: 'gasStation', targetEntity: GasStationStatusHistory::class)]
    private $gasStationStatusHistories;

    public function __construct()
    {
        $this->gasPrices = new ArrayCollection();
        $this->gasServices = new ArrayCollection();
        $this->gasStationStatusHistories = new ArrayCollection();
        $this->lastGasPrices = [];
        $this->lastGasPricesDecode = [];
    }

    public function __toString(): string
    {
        return $this->id;
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

    public function getClosedAt(): ?\DateTimeImmutable
    {
        return $this->closedAt;
    }

    public function setClosedAt(?\DateTimeImmutable $closedAt): self
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

    /**
     * @return array<mixed>
     */
    public function getLastGasPrices()
    {
        return $this->lastGasPrices;
    }

    public function setLastGasPrices(GasType $gasType, GasPrice $gasPrice): self
    {
        $this->lastGasPrices[$gasType->getId()] = [
            'id' => $gasPrice->getId(),
            'datetimestamp' => $gasPrice->getDateTimestamp(),
            'gasPriceValue' => $gasPrice->getValue(),
            'gasTypeId' => $gasPrice->getGasType()->getId(),
            'gasTypeLabel' => $gasPrice->getGasType()->getLabel(),
        ];

        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function getLastGasPricesDecode(): ?array
    {
        return $this->lastGasPricesDecode;
    }

    public function setLastGasPricesDecode(GasType $gasType, GasPrice $gasPrice): self
    {
        $this->lastGasPricesDecode[$gasType->getId()] = $gasPrice;

        return $this;
    }
}
