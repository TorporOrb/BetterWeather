<?php

namespace App\Controller;

use DateTime;
use App\Entity\Forecast;
use App\Entity\Location;
use App\Service\WeatherService;
use Doctrine\ORM\EntityManagerInterface;
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
    
    #[Route('/collect_data')]
public function collectData(
    WeatherService $weatherService,
    EntityManagerInterface $em): Response
{
    // Retrieve the forecast data from the WeatherService
    $forecastData = $weatherService->openJson();
    
    // Check if forecast data is available
    if ($forecastData === false) {
        // Handle the case where forecast data is not available
        return new JsonResponse(['error' => 'Forecast data not available'], Response::HTTP_NOT_FOUND);
    }

    // Retrieve the Location entity based on the location ID
    $locationRepository = $em->getRepository(Location::class);
    $location = $locationRepository->find($forecastData['location_id']);

    $dateTime = new DateTime($forecastData['dateTime']);
    // Check if the Location entity exists
    if (!$location) {
        // Handle the case where the Location entity does not exist
        return new JsonResponse(['error' => 'Location not found'], Response::HTTP_NOT_FOUND);
    }

    $forecast = new Forecast();

    $forecast->setLocation($location)
        ->setDateTime($dateTime)
        ->setTemperature($forecastData['temperature'])
        ->setFeelsLike($forecastData['feels_like'])
        ->setPressure($forecastData['pressure'])
        ->setHumidity($forecastData['humidity'])
        ->setWindSpeed($forecastData['wind_speed'])
        ->setWindDeg($forecastData['wind_deg'])
        ->setCloudiness($forecastData['cloudiness'])
        ->setIcon($forecastData['icon'])
    ;  
    // Persist the Forecast entity
    $em->persist($forecast);

    // Flush changes to the database
    $em->flush();


    return new JsonResponse(['message' => 'Forecast data persisted successfully'], Response::HTTP_CREATED);
}
}
