<?php

namespace App\Controller;


use DateTime;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomepageController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(): Response
    {
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

        return $this->render('homepage/index.html.twig', [
            'forecasts' => $forecasts,
         ]);
    }
}
