<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Vehicule;
use App\Form\CommandeType;
use App\Repository\MembreRepository;
use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommandeController extends AbstractController
{
    #[Route('/commande/add/{vehicule_id},from={date_start},to={date_end}', name: 'add_commande')]
    public function commande(int                    $vehicule_id,
                             string                 $date_start,
                             string                 $date_end,
                             EntityManagerInterface $entityManager,
                             VehiculeRepository     $vehiculeRepository,
                             MembreRepository       $membreRepository,
                             Request                $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $vehicule = $vehiculeRepository->find($vehicule_id);
        $membre = $membreRepository->findOneByPsuedo($this->getUser()->getUserIdentifier());

        $from = \DateTime::createFromFormat('U', $date_start);
        $to = \DateTime::createFromFormat('U', $date_end);
        $interval = $to->diff($from);

        $formValue = $this->createForm(CommandeType::class);
        $formValue->handleRequest($request);

        if ($formValue->isSubmitted() && $formValue->isValid()) {
            $commande = new Commande();

            $commande->setMembre($membre);
            $commande->setVehicule($vehicule);
            $commande->setDateHeureDepart($from);
            $commande->setDateHeureFin($to);
            $commande->setPrixTotal($interval->days * $vehicule->getPrixJournalier());
            $commande->setDateEnregistrement(new \DateTime());

            $entityManager->persist($commande);
            $entityManager->flush();

            flash()->addSuccess("Commande successful");

            return $this->redirectToRoute('home');
        }

        return $this->render('commande/index.html.twig', [
            'controller_name' => 'CommandeController',
            'form_value' => $formValue,
            'vehicule' => $vehicule,
            'currentUser' => $membre,
            'interval' => $interval,
            'from' => $from,
            'to' => $to,
        ]);
    }
}
