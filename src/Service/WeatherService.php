<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class WeatherService
{    

    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
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
    $jsonFilePath = '/home/pascal/Projects/Symfony/BetterWeather/public/forecast_data.json';

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

        // Initialize an array to store forecasts
        $forecasts = [];

        // Check if the 'Tilburg' key exists in the JSON data
        if (isset($jsonData['Tilburg'])) {
            // Access the forecast data for Tilburg
            $tilburgForecasts = $jsonData['Tilburg']['list'];
            $firstForecast = $tilburgForecasts[0];

            $icon = $this->getIcon($firstForecast['weather'][0]['main'],);

            // For example:
            $forecastValues = [
                'location_id' => 1,
                'dateTime' => $firstForecast['dt_txt'],
                'temperature' => $firstForecast['main']['temp'],
                'feels_like' => $firstForecast['main']['feels_like'],
                'pressure' => $firstForecast['main']['pressure'],
                'humidity' => $firstForecast['main']['humidity'],
                'wind_speed' => $firstForecast['wind']['speed'],
                'wind_deg' => $firstForecast['wind']['deg'],
                'cloudiness' => $firstForecast['clouds']['all'],
                'icon' => $icon,
            ];

            // Return the forecast data for Tilburg
            return $forecastValues;
        } else {
            return [];
        }
    } else {
        // Handle the case where the JSON file does not exist
        return false;
    }
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
