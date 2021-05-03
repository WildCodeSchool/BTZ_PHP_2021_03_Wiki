<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

// LI will allow validation with @assert functionnality 
use Symfony\Component\Validator\Constraints as Assert;
//LI needed to implement unicity of category name
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Mapping\ClassMetadata;


/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 */
class Category
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez indiquer un nom de catégorie")
     * @Assert\Length(max="255", maxMessage="La catégorie saisie est trop longue, elle ne doit pas dépasser 255 caractères")
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity=Article::class, inversedBy="categories")
     */
    private $articles;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Article[]
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        $this->articles->removeElement($article);

        return $this;
    }


    // LI function needed to implement uniqueEntity on title
    // it must be unique
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addConstraint(new UniqueEntity([
            'fields' => 'name',
            'errorPath' => 'port',
            'message' => 'Une catégorie avec ce nom existe déjà.',
        ]));
    }
}
