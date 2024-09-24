<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\IndexCountTemp;
use App\Entity\Divisions;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $divisionB = new IndexCountTemp();
        $divisionB ->setIndexId('0');
        $divisionB ->setStage('Division B');
        $manager->persist($divisionB);

        $divisionA = new IndexCountTemp();
        $divisionA ->setIndexId('0');
        $divisionA ->setStage('Division A');
        $manager->persist($divisionA);

        $quarterFinal = new IndexCountTemp();
        $quarterFinal ->setIndexId('0');
        $quarterFinal ->setStage('Quarterfinal');
        $manager->persist($divisionA);

        $semiFinal = new IndexCountTemp();
        $semiFinal ->setIndexId('0');
        $semiFinal ->setStage('Semifinal');
        $manager->persist($semiFinal);

        $bronzeMedal = new IndexCountTemp();
        $bronzeMedal ->setIndexId('0');
        $bronzeMedal ->setStage('BronzeMedal');
        $manager->persist($bronzeMedal);

        $grandFinal = new IndexCountTemp();
        $grandFinal ->setIndexId('0');
        $grandFinal ->setStage('Grandfinal');
        $manager->persist($grandFinal);
        $manager->flush();

        $divisions = new Divisions();
        $divisions ->setName('Division A');
        $divisions ->setDivisionId('1');
        $manager->persist($divisions);
        $manager->flush();

        $divisions = new Divisions();
        $divisions ->setName('Division B');
        $divisions ->setDivisionId('2');
        $manager->persist($divisions);
        $manager->flush();
    
    }
}