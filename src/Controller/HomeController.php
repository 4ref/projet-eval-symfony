<?php

namespace App\Controller;

use App\Form\HomeType;
use App\Repository\VehiculeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(Request            $request,
                         VehiculeRepository $vehiculeRepository): Response
    {
        $formValue = $this->createform(HomeType::class);
        $formValue->handleRequest($request);

        if ($formValue->isSubmitted() && $formValue->isValid()) {
            $vehicules = $vehiculeRepository->findAll();

            return $this->render('home/index.html.twig', [
                'controller_name' => 'HomeController',
                'form_value' => $formValue,
                'vehicules' => $vehicules,
                'dateStart' => $formValue->get('date_heure_depart')->getData(),
                'dateEnd' => $formValue->get('date_heure_fin')->getData(),
            ]);

            /*return $this->forward('App\\Controller\\VehiculesController::index', [
                'dateStart' => $formValue->get('date_heure_depart')->getData(),
                'dateEnd' => $formValue->get('date_heure_fin')->getData(),
            ]);*/
        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'form_value' => $formValue,
            'dateStart' => null,
            'dateEnd' => null,
        ]);
    }
}
