<?php

namespace App\Controller;

use App\Entity\Location;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/location')]
class LocationController extends AbstractController
{
    #[Route('/{city}', name: 'app_location')]
    public function index(
        EntityManagerInterface $em, 
        string $city
        ): Response
    {
        $location = $em->getRepository(Location::class)->findOneBy(['city_name' => $city]);
  
        $content = [
            'cityName' => $location->getCityName()
        ];

        return $this->render('location/index.html.twig', $content);
    }
}
