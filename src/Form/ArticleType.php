<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Tag;
use App\Entity\Version;

use App\Repository\VersionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

use Vich\UploaderBundle\Form\Type\VichImageType;

class ArticleType extends AbstractType
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $article = $options['data'];

        $versionRepository = $this->entityManager->getRepository(Version::class);
        $currentVersion = $article->getCurrentVersion();
        if($currentVersion){
            $version = $versionRepository->find($article->getCurrentVersion());
            $contentVersion = $version->getContent();
        } else {
            $contentVersion = '';
        }

        $builder
            ->add('title', TextType::class , ['label' => 'Nom'])
            ->add('description', TextareaType::class,['label' => 'Description'])
            ->add('content', CKEditorType::class,  ['mapped' => false, 'data' => $contentVersion , 'label' => 'Version en cours'],)
            ->add('imageFile', VichImageType::class, ['required' => false, 'label' => 'Image'])
            ->add('tags', EntityType::class, [
                'class' => Tag::class, 
                'label' => 'Mots clés',
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => false,
                'by_reference' => false,
            ])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'label' => 'Thèmes',
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => false,
                'by_reference' => false,
            ])
            ->add('content', CKEditorType::class,  ['mapped' => false])

            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
