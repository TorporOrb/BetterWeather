<?php

namespace App\Service;

use DateTime;
use App\Entity\Forecast;
use App\Entity\Location;
use App\Repository\LocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class WeatherService
{    

    private $params;
    private LocationRepository $locationRepository;
    private EntityManagerInterface $em;

    

    public function __construct(
        ParameterBagInterface $params,
        LocationRepository $locationRepository,
        EntityManagerInterface $em,
        )
    {
        $this->params = $params;
        $this->locationRepository = $locationRepository;
        $this->em = $em;
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

    public function openJson()
    {
    // Assuming your JSON file is located in the "public" directory
    $em = $this->em;
    $jsonFilePath = '/home/pascal/Projects/Symfony/BetterWeather/public/forecast_data.json';
    
    $citieData = $this->locationRepository->getCityNamesAndIds();

    // Check if the file exists
    if (file_exists($jsonFilePath)) {
        // Read the contents of the JSON file
        $jsonContents = file_get_contents($jsonFilePath);

        // Decode the JSON data into an associative array
        $jsonData = json_decode($jsonContents, true);

        // Check if decoding was successful
        if ($jsonData === null) {
            // Handle the case where JSON decoding failed
            return false;
        }


        foreach($citieData as $city)
        {
            $cityName = $city['city_name'];
            $locationId = $city['id'];
            // Check if the 'Tilburg' key exists in the JSON data
            if (isset($jsonData[$cityName])) {
                // Access the forecast data for Tilburg
                $cityForecasts = $jsonData[$cityName]['list'];
                $firstForecast = $cityForecasts[0];

                $icon = $this->getIcon($firstForecast['weather'][0]['main'],);

                // For example:
                $forecastData = [
                    'location_id' => $locationId,
                    'date' => $firstForecast['dt_txt'],
                    'temperature' => $firstForecast['main']['temp'],
                    'feels_like' => $firstForecast['main']['feels_like'],
                    'pressure' => $firstForecast['main']['pressure'],
                    'humidity' => $firstForecast['main']['humidity'],
                    'wind_speed' => $firstForecast['wind']['speed'],
                    'wind_deg' => $firstForecast['wind']['deg'],
                    'cloudiness' => $firstForecast['clouds']['all'],
                    'icon' => $icon,
                ];

                $this->writeForecastsToDatabase($forecastData);



                // Return the forecast data for Tilburg
                
                } else {
                    return [];
                }       
            } 
        } 

    $em->flush();
    return true;
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

    private function writeForecastsToDatabase(array $forecastData){

    $em = $this->em;
        // Check if forecast data is available
    if ($forecastData === false) {
        // Handle the case where forecast data is not available
        return false;
    }

    // Retrieve the Location entity based on the location ID
    $locationRepository = $em->getRepository(Location::class);
    $location = $locationRepository->find($forecastData['location_id']);

    $date = new DateTime($forecastData['date']);
    // Check if the Location entity exists
    if (!$location) {
        // Handle the case where the Location entity does not exist
        return false;
    }

    $forecast = new Forecast();

    $forecast->setLocation($location)
        ->setDate($date)
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

    }


}
