<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Truck;
use App\Entity\User;
use App\Entity\Product;
use App\Form\TruckRegisterType;
use App\Form\FormProductType;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Repository\TruckRepository;
use App\Repository\UserRepository;
use App\Repository\ProductRepository;


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
                $truck->setUser($this->getUser());
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

        // Methode pour routage et affichage de page personnalisé si connecté

        #[Route('/truck/mytruck/{id}', name: 'truck_mytruck')]
        public function mytruck($id, UserInterface $user){
            $data = $this->getDoctrine()->getRepository(Truck::class)->find($id);
            return $this->render('truck/mytruck.html.twig',[
                'truck' => $data,
                'id' => $user->getTruck()->getId(),
            ]);
        }

        #[Route('/truck/createproduct', name: 'truck_createproduct')]
        public function createproduct(Request $request,UserInterface $user){
            $product = new Product();
            $form = $this->createForm(FormProductType::class, $product);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()){
                $product->setTruck($this->getUser()->getTruck());
                $em = $this->getDoctrine()->getManager();
                $em->persist($product);
                $em->flush();
    
                $this->addFlash('notice','Enregistrement Réussi!');
    
                return $this->redirectToRoute('truck_createproduct');
            } else {
    
                $this->addFlash('notice','Votre inscription n\'a pas été prise en compte');
            }
    
            return $this->render('truck/createproduct.html.twig',[
                'form' => $form->createView(),
                'product_name' => 'Nom du produit',
                'type' => 'Catégorie',
                'price' => 'Prix',
                'description' => 'Description',
                
            ]);
        
            }
        }


