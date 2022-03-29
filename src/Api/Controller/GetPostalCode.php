<?php

namespace App\Api\Controller;

use App\Dto\GasStationMapDto;
use App\Repository\AddressRepository;
use App\Service\GasStationMapDtoService;
use Symfony\Component\HttpFoundation\Request;

class GetPostalCode
{
    public function __construct(
        private AddressRepository $addressRepository
    )
    {
    }

    public function __invoke(Request $request, $data): array
    {
        return $this->addressRepository->findPostalCodes();
    }
}