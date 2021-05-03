<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class TagFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i=0; $i < 30; $i++) { 
            $randomDatas = Faker\Factory::create('fr_FR');
            $tag = new Tag();
            $tag->setName($randomDatas->word);
            $manager->persist($tag);
            $this->addReference('tag' . $i, $tag);
        }
        $manager->flush();
    }
}
