<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class CategoryFixtures extends Fixture
{
    const CATEGORIES = [
        "Administration publique",
        "Aménagement du territoire",
        "Aménagement rural",
        "Aménagement urbain",
        "Architecture - Patrimoine",
        "Cadre juridique",
        "Circulation",
        "Collectivités territoriales",
        "Construction",
        "Démographie",
        "Economie",
        "Emploi - Formation - Éducation",
        "Environnement - Paysage",
        "Équipements",
        "Foncier - Propriété", "Habitat - logement", 
        "Information - Communication",
        "Infrastructures - Ouvrages d'art",
        "Ressources - Nuisances",
        "Santé",
        "Sciences humaines",
        "Tourisme - Loisirs",
        "Transports - Mobilité",
    ];

    public function load(ObjectManager $manager)
    {
        $i = 0;
        foreach(self::CATEGORIES as $value){
            $category = new Category();
            $category->setName($value);
            $manager->persist($category);
            $this->addReference('category_' . $i, $category);
            $i++;
        }
        $manager->flush();
    }
}
