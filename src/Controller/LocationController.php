<?php

namespace App\Controller;

use DateTime;
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
        // $location = $em->getRepository(Location::class)->findOneBy(['city_name' => $city]);
  
        $forecasts = [
            [
                "date" => new DateTime("2024-04-02"),
                "temperature" => 17,
                "feels_like" => 15,
                "pressure" => 1009,
                "humidity" => 88,
                "wind_speed" => 7.8,
                "wind_deg" => 169,
                "cloudiness" => 20,
                "icon" => "clouds",
            ],
            [
                "date" => new DateTime("2024-04-03"),
                "temperature" => 17,
                "feels_like" => 15,
                "pressure" => 1009,
                "humidity" => 88,
                "wind_speed" => 7.8,
                "wind_deg" => 169,
                "cloudiness" => 20,
                "icon" => "clouds",
            ],
            [
                "date" => new DateTime("2024-04-04"),
                "temperature" => 17,
                "feels_like" => 15,
                "pressure" => 1009,
                "humidity" => 88,
                "wind_speed" => 7.8,
                "wind_deg" => 169,
                "cloudiness" => 20,
                "icon" => "cloud-rain",
            ]
            ];

        $content = [
            'cityName' => $city,
            'forecasts' => $forecasts,
        
        ];

        

        return $this->render('location/index.html.twig', $content);
    }
}
