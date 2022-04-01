<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Address;
use App\Entity\Product;
use App\Entity\Truck;
use App\Entity\User;
use App\Repository\AddressRepository;
use App\Repository\TruckRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Service\CallApiServiceGPS;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(CallApiServiceGPS $callApiServiceGPS): Response
    {
        $allTrucksAdd = $this->getDoctrine()->getRepository(Address::class)->skipAddresses();
        $fullAdd = $this->getDoctrine()->getRepository(Address::class)->findAll();
        //$r =les noms de mes points gps
        $r = [];
        foreach($fullAdd as $data){
            $truck = $data->getTruck()->first();
            $truckName = $truck->getNameTruck();
            array_push($r,$truckName);
            }

        $addConcat = [];
        foreach($allTrucksAdd as $value){
        $number = $value['street_number'];    
        $street= $value['street_name'];
        $post_code= $value['post_code'];
        $city = $value['city'];
        //Je concaténe $street avec des +
        $street=str_replace(" ", "+", $street);
        //Je concatene tous les éléments de mon adresse avec des +
        $full = $number."+".$street."+".$post_code."+".$city;
        array_push($addConcat, $full);
        }
        // Je récupére les points gps de mes adresses dans $coordinate
        $coordinates =[];
        //Penser une fonction asynchrone pour la requete et stocker la réponse de l'API dans $coordonate
        foreach($addConcat as $value){
            $data = $callApiServiceGPS->getFranceApi($value);
            array_push($coordinates, $data);
        }
     
        for($i=0;$i<count($coordinates);$i++){
            array_push($coordinates[$i],$r[$i]);
            }
        //dd($coordinates);

        $coordinates = json_encode($coordinates);
        //dd($coordinates);


        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'coordinates' => $coordinates
        ]);
    }

    #[Route('/foodtrucks', name: 'foodtrucks')]
    public function foodtrucks(TruckRepository $truck)
    {
        
        $allTrucks = $this->getDoctrine()->getRepository(Truck::class)->truckCards();
        $trucksInfos = [];
        foreach($allTrucks as $truck){
            $info =[];
            $id = $truck['id'];
            array_push($info,$id);
            $name = $truck['name_truck'];
            array_push($info,$name);
            $style = $truck['style'];
            array_push($info,$style);
            array_push($trucksInfos, $info);
        }

        return $this->render('home/foodtrucks.html.twig', [
            'trucksInfos' => $trucksInfos,

        ]);
    }

    #[Route('/foodtruck/{id}', name: 'foodtruckCard')]
    public function foodtruckCard($id, TruckRepository $truck, ProductRepository $product, AddressRepository $address)
    {
        
        $data = $this->getDoctrine()->getRepository(Truck::class)->find($id);
        $value = $data->getId();
        $coord = $this->getDoctrine()->getRepository(Truck::class)->truckCoord($value);
        $boissons = $this->getDoctrine()->getRepository(Product::class)->byTruck($value, 'boisson');
        $sandwiches = $this->getDoctrine()->getRepository(Product::class)->byTruck($value, 'sandwich');
        $kebabs = $this->getDoctrine()->getRepository(Product::class)->byTruck($value, 'kebab');
        $menus = $this->getDoctrine()->getRepository(Product::class)->byTruck($value, 'menu');
        $specialites = $this->getDoctrine()->getRepository(Product::class)->byTruck($value, 'specialité');
        $addresses = $this->getDoctrine()->getRepository(Address::class)->findMyAddresses($value);

        return $this->render('home/foodtruckCard.html.twig', [
            'truck' => $coord,
            'boissons' => $boissons,
            'sandwiches' => $sandwiches,
            'kebabs' => $kebabs,
            'menus' => $menus,
            'specialites' => $specialites,
            'addresses' => $addresses,
        ]);
    }






}
