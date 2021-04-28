<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; $i++) {
            $fakeData = Faker\Factory::create('fr_FR');
            $category = new Category();
            $category->setName($fakeData->word);
            $manager->persist($category);
            $this->addReference('category_' . $i, $category);
        }
        $manager->flush();
    }
}
