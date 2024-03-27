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

}

