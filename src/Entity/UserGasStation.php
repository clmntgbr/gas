<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\UserGasStationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserGasStationRepository::class)]
#[ApiResource(
    collectionOperations: ['get', 'post'],
    itemOperations: ['get', 'delete'],
    denormalizationContext: ['groups' => 'user_gas_station.post'],
    routePrefix: '/auth'
)]
class UserGasStation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: GasStation::class)]
    #[ORM\JoinColumn(nullable: false), Groups('user_gas_station.post')]
    private GasStation $gasStation;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private User $user;

    public function __construct()
    {
        $this->id = rand();
    }

    public function getId(): ?int
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
