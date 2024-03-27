<?php 

namespace App\Service;

use DateTime;
use App\Entity\Forecast;
use App\Entity\Location;
use App\Repository\ForecastRepository;
use App\Repository\LocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpClient\HttpClient;

class DatabaseUpdater
{
    private $locationRepository;
    private $forecastRepository;
    private $entityManager;

    public function __construct(
        LocationRepository $locationRepository,
        ForecastRepository $forecastRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->locationRepository = $locationRepository;
        $this->forecastRepository = $forecastRepository;
        $this->entityManager = $entityManager;
    }

    public function fetchWeatherForecasts(): void
    {
        $cities = $this->locationRepository->getCitiesAndCoordinates();

        foreach ($cities as $cityName => $coordinates) {
            $forecast = $this->getWeather($coordinates['latitude'], $coordinates['longitude']);
            $this->processForecastData($forecast, $cityName);
        }
    }

    public function getWeather(float $lat, float $lon): array
    {
        $client = HttpClient::create();
        $response = $client->request('GET', 'https://api.openweathermap.org/data/2.5/forecast', [
            'query' => [
                'lat' => $lat,
                'lon' => $lon,
                'appid' => $_ENV['OPENWEATHER_API_KEY'],
                'units' => 'metric', 
            ],
        ]);

        return $response->toArray();
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

            $this->entityManager->persist($forecastEntity);
        }
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