<?php


namespace App\Command;

use App\Service\DatabaseUpdater;
use App\Service\WeatherForecastService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FetchWeatherDataCommand extends Command
{
    protected static $defaultName = 'fetch:weather-data';
    private $databaseUpdater;

    public function __construct(DatabaseUpdater $databaseUpdater)
    {
        $this->databaseUpdater = $databaseUpdater;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Fetches weather forecasts for cities and stores them in the database');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Fetch weather forecasts and store them in the database
        $this->databaseUpdater->fetchWeatherForecasts();

        $output->writeln('Weather forecasts fetched and stored successfully.');

        return Command::SUCCESS;
    }
}