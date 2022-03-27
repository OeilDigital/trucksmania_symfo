<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Address;
use App\Repository\AddressRepository;
use App\Repository\TruckRepository;
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
}
