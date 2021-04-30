<?php

namespace App\Form;

use App\Entity\Version;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class VersionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('comment')
            ->add('content')
            ->add('modification_date', ChoiceType::class, [
                'choices' => [
                    'now' => new \DateTime('now'),
                    'tomorrow' => new \DateTime('+1 day'),
                    '1 week' => new \DateTime('+1 week'),
                    '1 month' => new \DateTime('+1 month'),
                ],
                'group_by' => function($choice, $key, $value) {
                    if ($choice <= new \DateTime('+3 days')) {
                        return 'Soon';
                    }
            
                    return 'Later';
                },
            ])
            ->add('is_validated')
            ->add('contributor',null, ['choice_label' => 'email'])
            ->add('validator',null, ['choice_label' => 'email'])
            ->add('article',null, ['choice_label' => 'title'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Version::class,
        ]);
    }
}
