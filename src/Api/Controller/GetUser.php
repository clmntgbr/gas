<?php

namespace App\Api\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class GetUser
{
    public static $operationName = 'get_user';

    public function __construct(
        private Security $security
    )
    {
    }

    public function __invoke(Request $request, $data): User
    {
        return $this->security->getUser();
    }
}