<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    public const DEFAULT_ENV = __DIR__ . '/../../.env';
    public const LOCAL_ENV = __DIR__ . '/../../.env.local';

    #[Route('/app2', name: 'app_app2')]
    public function index(): Response
    {
        $env = self::LOCAL_ENV;
        if (false === file_exists($env)) {
            $env = self::DEFAULT_ENV;
        }

        $dotenv = new Dotenv();
        $dotenv->loadEnv($env);

        dd($_ENV);
    }
}
