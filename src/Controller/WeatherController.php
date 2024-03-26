<?php

namespace App\Controller;

use DateTime;
use App\Repository\LocationRepository;
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
    public function fetchWeatherForecasts(
        LocationRepository $locationRepository,
        WeatherService $weatherService
        ): JsonResponse
    {
        $cities = $locationRepository->getCitiesAndCoordinates();
        
        $forecastData = [];

        foreach ($cities as $cityName => $coordinates) {
            
            // Call the getWeather function for each city
            $forecast = $weatherService->getWeather($coordinates['latitude'], $coordinates['longitude']);
            
            // Store the forecast data for the current city in the $forecastData array
            $forecastData[$cityName] = $forecast;
        }

        $filePath = 'forecast_data.json';
        file_put_contents($filePath, json_encode($forecastData, JSON_PRETTY_PRINT));

        // Return the $forecastData array as a JSON response
        return new JsonResponse($forecastData);
    }
    
    #[Route('/collect_data')]
public function collectData(
    WeatherService $weatherService,
    ): Response
{
    // Retrieve the forecast data from the WeatherService
    $forecastSucces = $weatherService->openJson();

    if (!$forecastSucces){
        return new JsonResponse(['error' => 'Forecast data not available'], Response::HTTP_NOT_FOUND);
    }
    

    return new JsonResponse(['message' => 'Forecast data persisted successfully'], Response::HTTP_CREATED);
}
}
