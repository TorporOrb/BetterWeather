<?php

namespace App\Controller;

use DateTime;
use App\Entity\Forecast;
use App\Entity\Location;
use App\Repository\LocationRepository;
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
    
    // // Check if forecast data is available
    // if ($forecastData === false) {
    //     // Handle the case where forecast data is not available
    //     return new JsonResponse(['error' => 'Forecast data not available'], Response::HTTP_NOT_FOUND);
    // }

    // // Retrieve the Location entity based on the location ID
    // $locationRepository = $em->getRepository(Location::class);
    // $location = $locationRepository->find($forecastData['location_id']);

    // $dateTime = new DateTime($forecastData['dateTime']);
    // // Check if the Location entity exists
    // if (!$location) {
    //     // Handle the case where the Location entity does not exist
    //     return new JsonResponse(['error' => 'Location not found'], Response::HTTP_NOT_FOUND);
    // }

    // $forecast = new Forecast();

    // $forecast->setLocation($location)
    //     ->setDateTime($dateTime)
    //     ->setTemperature($forecastData['temperature'])
    //     ->setFeelsLike($forecastData['feels_like'])
    //     ->setPressure($forecastData['pressure'])
    //     ->setHumidity($forecastData['humidity'])
    //     ->setWindSpeed($forecastData['wind_speed'])
    //     ->setWindDeg($forecastData['wind_deg'])
    //     ->setCloudiness($forecastData['cloudiness'])
    //     ->setIcon($forecastData['icon'])
    // ;  
    // // Persist the Forecast entity
    // $em->persist($forecast);

    // // Flush changes to the database
    // $em->flush();


    return new JsonResponse(['message' => 'Forecast data persisted successfully'], Response::HTTP_CREATED);
}
}
