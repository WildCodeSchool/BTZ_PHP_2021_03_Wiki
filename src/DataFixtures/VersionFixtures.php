<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Version;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker;

class VersionFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $counter = 0;
        for ($i=0; $i < 20; $i++) {
            $article = $this->getReference('article_' . $i);
            for ($j=0; $j < 3; $j++) {
                $faker = Faker\Factory::create('fr_FR');
                $version = new Version();
                $version->setComment($faker->sentence(15));
                $version->setContent($faker->text);
                $version->setModificationDate($faker->dateTimeThisYear);
                $version->setIsValidated(false);
                $version->setContributor($this->getReference('user_' . rand(0, 4)));
                $version->setArticle($article);
                $manager->persist($version);
                $manager->flush();
                $this->setReference('version_' . $counter, $version);
                $counter++;
            }
            $article->setCurrentVersion($version->getId());
            // mettre current_version à article
        }
    }

    public function getDependencies()
    {
        return [ArticleFixtures::class, UserFixtures::class];
    }
}
