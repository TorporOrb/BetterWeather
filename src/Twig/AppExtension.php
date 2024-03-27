<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    private $cityNames; // Change the property name

    public function __construct(array $cityNames) // Change the argument name
    {
        $this->cityNames = $cityNames; // Change the property assignment
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_city_names', [$this, 'getCityNames']), // Change the function name
        ];
    }

    public function getCityNames(): array // Change the function name
    {
        return $this->cityNames; // Change the property access
    }
}
