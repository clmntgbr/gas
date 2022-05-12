<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    #[Route('/test', name: 'test')]
    public function test(Request $request): JsonResponse
    {
        return JsonResponse::fromJsonString('{"test": "test"}');
    }
}
