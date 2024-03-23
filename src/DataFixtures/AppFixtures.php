<?php

namespace App\DataFixtures;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $locationFixtures = new LocationFixtures();
        $locationFixtures->load($manager);

        $manager->flush();
    }
}
