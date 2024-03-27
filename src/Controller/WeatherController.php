<?php

namespace App\Controller;

use DateTime;
use App\Entity\Forecast;
use App\Entity\Location;
use App\Service\WeatherService;
use App\Repository\ForecastRepository;
use App\Repository\LocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/weather')]
class WeatherController extends AbstractController
{
    private $locationRepository;
    private $forecastRepository;
    private $entityManager;

    public function __construct(
        LocationRepository $locationRepository,
        ForecastRepository $forecastRepository,
        EntityManagerInterface $entityManager,
        )
    {
        $this->locationRepository = $locationRepository;
        $this->forecastRepository = $forecastRepository;
        $this->entityManager = $entityManager;    
    }

    
    #[Route('/fetch_weather_forecasts')]
public function fetchWeatherForecasts(
    WeatherService $weatherService,
): JsonResponse
{
    $cities = $this->locationRepository->getCitiesAndCoordinates();
    
    foreach ($cities as $cityName => $coordinates) {
        // Call the getWeather function for each city
        $forecast = $weatherService->getWeather($coordinates['latitude'], $coordinates['longitude']);
        
        // Store the forecast data for the current city in the database
        $this->processForecastData($forecast, $cityName);
    }

    // Return a success message
    return new JsonResponse(['message' => 'Forecast data persisted successfully'], Response::HTTP_CREATED);
}

    private function processForecastData(
        array $forecastData, 
        string $cityName, 
        ): void
    {
        $citieData = $this->locationRepository->getCityNamesAndIds();
        $locationId = null;

        foreach ($citieData as $city) {
            if ($city['city_name'] === $cityName) {
                $locationId = $city['id'];
                break;
            }
        }

        if (!$locationId) {
            // Handle the case where the city is not found in the database
            return;
        }

        foreach ($forecastData['list'] as $thisForecast) {
            $icon = $this->getIcon($thisForecast['weather'][0]['main']);
            $dateString = $thisForecast['dt_txt'];
            $location = $this->entityManager->getRepository(Location::class)->find($locationId);


            $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $dateString);   
   

            $forecastEntity = new Forecast();
            $forecastEntity->setLocation($location);
            $forecastEntity->setDate($dateTime);
            $forecastEntity->setTemperature($thisForecast['main']['temp']);
            $forecastEntity->setFeelsLike($thisForecast['main']['feels_like']);
            $forecastEntity->setPressure($thisForecast['main']['pressure']);
            $forecastEntity->setHumidity($thisForecast['main']['humidity']);
            $forecastEntity->setWindSpeed($thisForecast['wind']['speed']);
            $forecastEntity->setWindDeg($thisForecast['wind']['deg']);
            $forecastEntity->setCloudiness($thisForecast['clouds']['all']);
            $forecastEntity->setIcon($icon);

            // Persist the forecast entity
            $this->entityManager->persist($forecastEntity);
        }

        // Flush the changes to the database
        $this->entityManager->flush();
    }

    private function getIcon($string): string
    {
        switch($string){
            case 'Clear':
                return 'sun';
            case 'Clouds':
                return 'clouds';
            case 'Rain':
                return 'cloud-rain';
            case 'Thunderstorm':
                return 'cloud-lightning-rain';
            case 'Snow':
                return 'cloud-snow';
            case 'Mist': 
                return 'cloud-fog';
            case 'Fog': 
                return 'cloud-fog';
            case 'Drizzle': 
                return 'cloud-drizzle';
            case 'Haze':
                return 'cloud-haze';
            default:
                return "question";   

        }
    }
}