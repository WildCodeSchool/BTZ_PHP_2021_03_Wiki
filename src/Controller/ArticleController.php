<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Version;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Repository\VersionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface; // Nous appelons le bundle KNP Paginator

/**
 * @Route("/article")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="article_index", methods={"GET"})
     */
   
        public function index(Request $request, PaginatorInterface $paginator) // Nous ajoutons les paramètres requis
        {
            // Méthode findBy qui permet de récupérer les données avec des critères de filtre et de tri
            $donnees = $this->getDoctrine()->getRepository(Article::class)->findBy([],['creation_date' => 'desc']);
    
            $articles = $paginator->paginate(
                $donnees, // Requête contenant les données à paginer (ici nos articles)
                $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
                12 // Nombre de résultats par page
            );
            
            return $this->render('article/index.html.twig', [
                'articles' => $articles,
            ]);
        }
        
    

    /**
     * @Route("/new", name="article_new", methods={"GET","POST"})
     */
    public function new(Request $request, MailerInterface $mailer): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            $entityManager = $this->getDoctrine()->getManager();
            $currentUser = $this->getUser();

            //On hydrate l'article des données manquantes
            $article->setCreator($currentUser);
            $article->setIsPublished(false);
            $article->setIsDeleted(false);
            $article->setCreationDate(new \DateTime());

            //On créé une version (la première de l'article)
            $version = new Version();
            $version->setArticle($article);
            $version->setComment("Première version de l'article");

            //Récupére le contenu de l'input content et le met dans version.content
            $version->setContent($form->get('content')->getData());

            $version->setContributor($currentUser);
            $version->setIsValidated(false);
            $version->setModificationDate(new \DateTime());

            //Ajouter version aux versions et à current_version_id
            $article->addVersion($version);

            $entityManager->persist($article);
            $entityManager->persist($version);
            // $entityManager->flush();

            $article->setCurrentVersion($version->getId());
            $article->setCreator($currentUser);
            $entityManager->persist($article);
            $entityManager->flush();

            $email = (new Email())
            ->from('from@example.com')
            ->to('to@example.com')
            ->subject('Une nouvelle article vient d\'être publiée !')
            ->html('<p>Une nouvelle article vient d\'être publiée sur Wiki !</p>');

            $mailer->send($email);

            return $this->redirectToRoute('article_index');
        }

        return $this->render('article/new.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/{version_id?current}", name="article_show", methods={"GET"})
     */
    public function show(Article $article, VersionRepository $versionRepository, String $version_id): Response
    {
        if ($version_id == "current") {
            $version = $versionRepository->find($article->getCurrentVersion());
        } else {
            $version = $versionRepository->find($version_id);
        }
        $lastVersions = $versionRepository->findBy(['article' => $article->getId()], ['modification_date' => 'DESC'], 3);
        return $this->render('article/show.html.twig', [
            'article' => $article,
            'version' => $version,
            'lastVersions' => $lastVersions
        ]);
    }

    /**
     * @Route("/{id}/versions", name="article_versions", methods={"GET"})
     */
    public function showArticleVersions(Article $article, VersionRepository $versionRepository): Response
    {
        $allVersions = $versionRepository->findBy(['article' => $article->getId()], ['modification_date' => 'DESC']);
        return $this->render('article/versions.html.twig', [
            'article' => $article,
            'allVersions' => $allVersions
        ]);
    }

    /**
     * @Route("/{id}/edit", name="article_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Article $article, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentUser = $this->getUser();
            $article->setCreator($currentUser);
            $article->setCreationDate(new \DateTime());

            // créer une nouvelle version
            $version = new Version();
            $version->setContent($form->get('content')->getData());
            $version->setModificationDate(new \DateTime());
            $version->setIsValidated(false);
            $version->setContributor($currentUser);
            $version->setArticle($article);
            $this->getDoctrine()->getManager()->persist($version);
            $this->getDoctrine()->getManager()->flush();

            $article->addVersion($version);
            $article->setCurrentVersion($version->getId());

            $this->getDoctrine()->getManager()->flush();


            $email = (new Email())

                ->from('from@example.com')

                ->to('to@example.com')

            ->subject('Un article vient d\'être modifié !')

            ->html('<p>Un article vient d\'être modifié sur le Wiki !</p>');

            $mailer->send($email);

            return $this->redirectToRoute('article_index');
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="article_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Article $article): Response
    {
        if ($this->isCsrfTokenValid('delete' . $article->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('article_index');
    }
}
