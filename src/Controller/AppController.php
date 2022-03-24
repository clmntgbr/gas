<?php

namespace App\Controller;

use App\Entity\GasPrice;
use App\Entity\GasStation;
use App\Entity\GasType;
use App\Lists\GasTypeReference;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    public const DEFAULT_ENV = __DIR__ . '/../../.env';
    public const LOCAL_ENV = __DIR__ . '/../../.env.local';

    #[Route('/app2', name: 'app_app2')]
    public function index(EntityManagerInterface $entity): Response
    {
        $gasType = $entity->getRepository(GasType::class)->findOneBy(['reference' => GasTypeReference::GAZOLE]);
        $gasStation = $entity->getRepository(GasStation::class)->findOneBy(['id' => 94000012]);
        $gasPrices = $entity->getRepository(GasPrice::class)->findBy(['gasStation' => $gasStation, 'gasType' => $gasType], ['id' => 'DESC']);
        dump($gasStation);
        dump($gasPrices);
        die;
    }

    #[Route('/app1', name: 'app_app1')]
    public function index1(EntityManagerInterface $em): Response
    {
        $gasStation = $em->getRepository(GasStation::class)->findOneBy(['id' => 94120010]);
        dd($gasStation);
    }
}
