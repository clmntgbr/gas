<?php

namespace App\Api\Controller;

use App\Dto\GetPostalCodesDto;
use App\Repository\AddressRepository;

class GetPostalCodes
{
    public function __construct(
        private AddressRepository $addressRepository
    ){
    }

    public function __invoke($data): GetPostalCodesDto
    {
        $GetPostalCodesDto = new GetPostalCodesDto();
        $GetPostalCodesDto->postalCodes = $this->addressRepository->getPostalCodes();
        return $GetPostalCodesDto;
    }
}