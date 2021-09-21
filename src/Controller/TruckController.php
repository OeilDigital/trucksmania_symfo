<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Truck;
use App\Entity\User;
use App\Form\TruckRegisterType;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Repository\TruckRepository;


class TruckController extends AbstractController
{
    #[Route('/truck', name: 'truck')]
    public function index(UserInterface $user): Response
    {
        return $this->render('truck/index.html.twig', [
            'username' => $user->getUsername(),

        ]);
    }

        #[Route('/truck/create', name: 'create')]

        public function create(Request $request, UserInterface $user){
            $truck = new Truck();
            $form = $this->createForm(TruckRegisterType::class, $truck);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()){
                $truck->setUser($this->getUser($id));
                $em = $this->getDoctrine()->getManager();
                $em->persist($truck);
                $em->flush();
    
                $this->addFlash('notice','Enregistrement Réussi!');
    
                return $this->redirectToRoute('truck');
            } else {
    
                $this->addFlash('notice','Votre inscription n\'a pas été prise en compte');
            }
    
            return $this->render('truck/create.html.twig',[
                'form' => $form->createView(),
                'name_truck' => 'Nom d\'enseigne',
                'last_name' => 'Nom de famille',
                'first_name' => 'Prénom',
                'phone_number' => 'Téléphone',
                'siret' => 'Numéro de siret',
            ]);
    
        }

        #[Route('/truck/mytruck/{id}', name: 'truck_mytruck')]
        public function mytruck($id, UserInterface $user){
            $data = $this->getDoctrine()->getRepository(Truck::class)->find($id);
            return $this->render('truck/mytruck.html.twig', [
                'truck' => $data,
                'username' => $user->getUsername()
            ]);
        }

}

