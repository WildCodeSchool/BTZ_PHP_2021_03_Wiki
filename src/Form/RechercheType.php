<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RechercheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
           // ->add('image')
            //->add('updatedAt')
            ->add('title')
           ->add('Chercher',SubmitType::class)
         // ->add('description')
           // ->add('picture')
            //->add('is_published')
           // ->add('is_deleted')
            //->add('publication_date')
            //->add('creation_date')
           // ->add('currentVersion')
            //->add('tags')
            //->add('categories')
            //->add('creator')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
