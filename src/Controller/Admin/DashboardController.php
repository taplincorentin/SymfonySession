<?php

namespace App\Controller\Admin;

use App\Entity\Session;
use Symfony\Component\HttpFoundation\Response;
use App\Controller\Admin\SessionCrudController;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
       $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
       return $this->redirect($adminUrlGenerator->setController(SessionCrudController::class));
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('SymfonySession');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('Sessions', 'fa fa-home', Session::class);
        
    }
}
