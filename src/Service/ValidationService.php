<?php 

namespace App\Service;

class ValidationService
{
    public function isValidCity(string $cityName): bool
    {
        $cityNames = ['Tilburg', 'Amsterdam', 'Rotterdam', 'Den Haag', 'Utrecht', 'Maastricht', 'Eindhoven', 'Nijmegen'];
        return in_array($cityName, $cityNames);
    }
}