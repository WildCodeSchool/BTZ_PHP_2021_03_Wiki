<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TagFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 15; $i++) {
            $fakeTag = Faker\Factory::create('fr_FR');
            $tag = new Tag();
            $tag->setName($fakeTag->word);
            $manager->persist($tag);
            $this->addReference('tag_' . $i, $tag);
        }
        $manager->flush();
    }
}
