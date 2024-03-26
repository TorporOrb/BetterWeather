<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Location;

class LocationFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $cities = [
            'Tilburg' => [51.5500, 5.0833],
            'Amsterdam' => [52.3728, 4.8936],
            'Rotterdam' => [51.9200, 4.4800],
            'Den Haag' => [52.0786630, 4.2887880],
            'Utrecht' => [52.0908, 5.1217],
            'Maastricht' => [50.8500, 5.6833],
            'Eindhoven' => [51.4333, 5.4833],
            'Nijmegen' => [51.8475, 5.8625]
        ];

        foreach ($cities as $city => $coordinates )
        {
            $lat = $coordinates[0];
            $lon = $coordinates[1];

            $this->makeFixture($manager, $city, $lat, $lon);
        }    

        $manager->flush();
    }

    public function makeFixture(
        ObjectManager $manager, 
        $name, 
        $lat, 
        $lon): Location
    {
        $location = new Location();
        $location->setCityName($name)
            ->setLatitude($lat)
            ->setLongitude($lon)
        ;
        $manager->persist($location);
        return $location;
    }
}
