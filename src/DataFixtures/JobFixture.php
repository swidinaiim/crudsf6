<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Job;

class JobFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $data = [

            "Medical Professionals",
            "Data Scientist",
            "Machine Learning Experts",
            "Blockchain Developer",
            "Full Stack Software Developer",
            "Product Management",
            "Management Consultant",
            "Investment Banker"
        ];
        for($i = 0 ; $i < count($data) ; $i++){
            $job = new Job();
            $job->setDesignation($data[$i]);
            $manager->persist($job);
            
        }

        $manager->flush();
    }
}
