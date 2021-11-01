<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Truck;
use App\Entity\User;
use App\Entity\Product;
use App\Entity\Address;
use App\Form\TruckRegisterType;
use App\Form\FormProductType;
use App\Form\FormAddressType;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Repository\TruckRepository;
use App\Repository\UserRepository;
use App\Repository\ProductRepository;
use App\Repository\AddressRepository;


class TruckController extends AbstractController
{
    #[Route('/truck', name: 'truck')]
    public function index(UserInterface $user): Response
    {
        return $this->render('truck/index.html.twig', [
            'username' => $user->getUsername(),

        ]);
    }

    // Creation d'un profil Truck suite à inscription User

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
                'style' => 'Style',
                'last_name' => 'Nom de famille',
                'first_name' => 'Prénom',
                'phone_number' => 'Téléphone',
                'siret' => 'Numéro de siret',
            ]);
    
        }

//  Mise à jour des informations d'inscription Truck

        #[Route('/trucks/update/{id}', name: "update")]
        public function update(Request $request,UserInterface $user,$id){
            $truck = $this->getDoctrine()->getRepository(Truck::class)->find($id);
            $form = $this->createForm(TruckRegisterType::class, $truck);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()){
                $truck->setUser($this->getUser());
                $em = $this->getDoctrine()->getManager();
                $em->persist($truck);
                $em->flush();
        
                $this->addFlash('notice','Mise à jour réussie!');
        
                return $this->redirectToRoute('truck');
            }
        
            return $this->render('truck/update.html.twig',[
                'form' => $form->createView(),
                'name_truck' => 'Nom d\'enseigne',
                'style' => 'Style',
                'last_name' => 'Nom de famille',
                'first_name' => 'Prénom',
                'phone_number' => 'Téléphone',
                'siret' => 'Numéro de siret',
            ]);
        
            }

// Methode pour routage et affichage de page personnalisé si connecté

        #[Route('/truck/mytruck/{id}', name: 'truck_mytruck')]
        public function mytruck($id,ProductRepository $product, AddressRepository $address, UserInterface $user){
            $data = $this->getDoctrine()->getRepository(Truck::class)->find($id);
            $products = $this->getDoctrine()->getRepository(Product::class)->findBy(array('truck' => $user->getTruck()));
            $addresses = $this->getDoctrine()->getRepository(Address::class)->findBy(array('truck' => $user->getTruck()));
            
            return $this->render('truck/mytruck.html.twig',[
                'truck' => $data,
                'products' => $products,
                'addresses' => $addresses,
                'id' => $user->getTruck()->getId(),
            ]);
        }


// Creation de produits

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

    // Affichage de l'ensemble des produit par utilisateur

    #[Route('/truck/menu', name: 'truck_menu')]
    public function menu(ProductRepository $product, UserInterface $user){
    $data = $this->getDoctrine()->getRepository(Product::class)->findBy(array('truck' => $user->getTruck()));
    // $data = $this->getDoctrine()->getRepository(Product::class)->findBy([], ['truck_id' => $product->getTruck($this->getUser()->getTruck())]);
    return $this->render('truck/menu.html.twig', [
        'list' => $data,
    ]);
    }

// Mise à jour des produit


    #[Route('/truck/updateproduct/{id}', name: "updateproduct")]

    public function updateproduct(Request $request, UserInterface $user, $id){
    $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
    $form = $this->createForm(FormProductType::class, $product);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()){
        $product->setTruck($this->getUser()->getTruck());
        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();

        $this->addFlash('notice','Mise à jour réussie!');

        return $this->redirectToRoute('truck');
    }

    return $this->render('truck/updateproduct.html.twig',[
        'form' => $form->createView(),
        'product_name' => 'Nom du produit',
        'type' => 'Catégorie',
        'price' => 'Prix',
        'description' => 'Description',
        
    ]);

    }

// Supprimer un produit

     #[Route('/truck/delete/{id}', name: "delete")]
     public function delete($id){
        $data = $this->getDoctrine()->getRepository(Product::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($data);
        $em->flush();

        $this->addFlash('notice','Données effacées!');

        return $this->redirectToRoute('truck');
        

    }

// Gestion des adresses

// Crèation d'une nouvelle adressse

    #[Route('/truck/createaddress', name: 'createaddress')]
    public function createaddress(Request $request,UserInterface $user){
        $address = new Address();
        $form = $this->createForm(FormAddressType::class, $address);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $address->addTruck($this->getUser()->getTruck());
            $em = $this->getDoctrine()->getManager();
            $em->persist($address);
            $em->flush();

            $this->addFlash('notice','Enregistrement Réussi!');

            return $this->redirectToRoute('truck');
        } else {

            $this->addFlash('notice','Votre inscription n\'a pas été prise en compte');
        }

        return $this->render('truck/createaddress.html.twig',[
            'form' => $form->createView(),
            'street_number' => 'N° de voirie',
            'street_name' => 'Nom de voirie',
            'post_code' => 'Code Postal',
            'city' => 'Ville',
            'additional_address' => 'Informations complémentaires'
            
        ]);
    
    }

    // Liste des adresses

        #[Route('/truck/listaddress', name: 'truck_listaddress')]
        public function listaddress(AddressRepository $address, UserInterface $user){
            $data = $this->getDoctrine()->getRepository(Address::class)->findBy(array('truck' => $user->getTruck() ));
            return $this->render('truck/listaddress.html.twig', [
                'list' => $data,
            ]);
            }


    // Mise à jour d'une adresse

        #[Route('/truck/updateaddress/{id}', name: "updateaddress")]
        public function updateaddress(Request $request, UserInterface $user, $id){
            $address = $this->getDoctrine()->getRepository(Address::class)->find($id);
            $form = $this->createForm(FormAddressType::class, $address);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()){
                $address->addTruck($this->getUser()->getTruck());
                $em = $this->getDoctrine()->getManager();
                $em->persist($address);
                $em->flush();
        
                $this->addFlash('notice','Mise à jour réussie!');
        
                return $this->redirectToRoute('listaddress');
            }
        
            return $this->render('truck/updateaddress.html.twig',[
                'form' => $form->createView(),
                'street_number' => 'N° de voirie',
                'street_name' => 'Nom de voirie',
                'post_code' => 'Code Postal',
                'city' => 'Ville',
                'additional_address' => 'Informations complémentaires'
                
            ]);
        
            }

        #[Route('/truck/deleteaddress/{id}', name: "deleteaddress")]

        public function deleteAddress($id){
        $data = $this->getDoctrine()->getRepository(Address::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($data);
        $em->flush();

        $this->addFlash('notice','Données effacées!');

        return $this->redirectToRoute('listaddress');
        

    } 






}





