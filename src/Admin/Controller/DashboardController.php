<?php

namespace App\Admin\Controller;

use App\Entity\Address;
use App\Entity\Currency;
use App\Entity\GasPrice;
use App\Entity\GasService;
use App\Entity\GasStation;
use App\Entity\GasStationStatus;
use App\Entity\GasType;
use App\Entity\GooglePlace;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('@EasyAdmin/page/content.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('App');
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user)
            ->setName($user->getEmail());
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToUrl('Api Docs', 'fas fa-map-marker-alt', '/api/docs');
        yield MenuItem::linkToCrud('Gas Stations', 'fas fa-map-marker-alt', GasStation::class);
        yield MenuItem::linkToCrud('Gas Types', 'fas fa-map-marker-alt', GasType::class);
        yield MenuItem::linkToCrud('Gas Prices', 'fas fa-map-marker-alt', GasPrice::class);
        yield MenuItem::linkToCrud('Gas Services', 'fas fa-map-marker-alt', GasService::class);
        yield MenuItem::linkToCrud('Gas Station Status', 'fas fa-map-marker-alt', GasStationStatus::class);
        yield MenuItem::linkToCrud('Google Places', 'fas fa-map-marker-alt', GooglePlace::class);
        yield MenuItem::linkToCrud('Address', 'fas fa-map-marker-alt', Address::class);
        yield MenuItem::linkToCrud('Currencies', 'fas fa-map-marker-alt', Currency::class);
        yield MenuItem::linkToCrud('User', 'fas fa-map-marker-alt', User::class);
    }
}
