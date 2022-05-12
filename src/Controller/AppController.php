<?php

namespace App\Controller;

use App\Service\RabbitMQService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    #[Route('/test', name: 'test')]
    public function test(RabbitMQService $rabbitMQService, Request $request): JsonResponse
    {
        $rabbitMQService->getQueues();
        return JsonResponse::fromJsonString('{"test": "test"}');
    }
}
