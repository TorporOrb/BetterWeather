<?php

namespace App\Controller;

use App\Service\WeatherService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/weather')]
class WeatherController extends AbstractController
{
    #[Route('/', name: 'app_weather')]
    public function index(): Response
    {
        return $this->render('weather/index.html.twig', [
            'controller_name' => 'WeatherController',
        ]);
    }

    #[Route('/fetch_weather_forecasts')]
    public function fetchWeatherForecasts(WeatherService $weatherService): JsonResponse
    {
        $cities = [
            'Tilburg' => [51.5500, 5.0833],
            'Amsterdam' => [52.3728, 4.8936],
            'Rotterdam' => [51.9200, 4.4800],
            'Den Haag' => [52.0800, 4.3100],
            'Utrecht' => [52.0908, 5.1217],
            'Maastricht' => [50.8500, 5.6833],
            'Eindhoven' => [51.4333, 5.4833],
            'Nijmegen' => [51.8475, 5.8625]
        ];
        
        $forecastData = [];

        foreach ($cities as $city => $coordinates) {
            $latitude = $coordinates[0];
            $longitude = $coordinates[1];
            
            // Call the getWeather function for each city
            $forecast = $weatherService->getWeather($latitude, $longitude);
            
            // Store the forecast data for the current city in the $forecastData array
            $forecastData[$city] = $forecast;
        }

        $filePath = 'forecast_data.json';
        file_put_contents($filePath, json_encode($forecastData, JSON_PRETTY_PRINT));

        // Return the $forecastData array as a JSON response
        return new JsonResponse($forecastData);
    }
}
