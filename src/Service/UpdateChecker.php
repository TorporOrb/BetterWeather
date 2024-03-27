<?php

namespace App\Service;

use DateTime;
use App\Repository\ForecastRepository;

class UpdateChecker
{
    private $forecastRepository;

    public function __construct(ForecastRepository $forecastRepository)
    {
        $this->forecastRepository = $forecastRepository;
    }

    public function isForecastWithin3Hours(): bool
    {
        $currentDateTime = new DateTime(); 
        $timestamp = $this->forecastRepository->getFirstForecastTimestamp();
        $forecastDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $timestamp);

        // Calculate the difference in seconds between the current time and the forecast timestamp
        $timeDiffSeconds = $currentDateTime->getTimestamp() - $forecastDateTime->getTimestamp();

        // Check if the difference is less than or equal to 3 hours
        return $timeDiffSeconds <= (3 * 60 * 60);
    }
}