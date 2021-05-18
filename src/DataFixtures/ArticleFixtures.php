<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Article;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker;

class ArticleFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for ($i=0; $i < 20; $i++) {
            $faker = Faker\Factory::create('fr_FR');
            $article = new Article();
            $article->setTitle($faker->word);
            $article->setDescription($faker->text);
            $article->setCreationDate($faker->dateTimeThisYear);
            $article->setIsPublished(false);
            $article->setIsDeleted(false);
            $article->setCreator($this->getReference('user_' . rand(0, 4)));
            $manager->persist($article);
            $this->setReference('article_' . $i, $article);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [UserFixtures::class];
    }
}
