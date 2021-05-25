<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Version;
use App\Form\ArticleType;
use App\Form\RechercheType;
use Symfony\Component\Mime\Email;
use App\Repository\ArticleRepository;
use App\Repository\VersionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Knp\Component\Pager\PaginatorInterface; // Nous appelons le bundle KNP Paginator
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

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
        $donnees = $this->getDoctrine()->getRepository(Article::class)->findBy([], ['creation_date' => 'desc']);

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
    * @Route("/recherche", name="search")
    */
    public function search(Request $request, ArticleRepository $articleRepository, PaginatorInterface $paginator)
    {
        $query = $request->query->get('search');

        if (!empty($query)) {
            $datas = $articleRepository->search($query);

            // Paginate the results of the query
            $articles = $paginator->paginate(
            // Doctrine Query, not results
            $datas,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            10
            );

            return $this->render('article/search.html.twig', [
                'articles' => $articles,
            ]);
        }
        
        return $this->redirectToRoute('article_index');
    }

    /**
     * @Route("/new", name="article_new", methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     */
    public function new(Request $request, MailerInterface $mailer, ArticleRepository $articleRepository): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            $entityManager = $this->getDoctrine()->getManager();
            $currentUser = $this->getUser();

            if($article->getMonthlyArticle()){
                $monthlyArticleOld = $articleRepository->findOneBy(['monthly_article' => true]);
                if($monthlyArticleOld){
                    $monthlyArticleOld->setMonthlyArticle(false);
                }
            }


            //On hydrate l'article des données manquantes
            $article->setCreator($currentUser)
            ->setIsPublished(false)
            ->setIsDeleted(false)
            ->setCreationDate(new \DateTime());

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
            $entityManager->flush();

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
            $this -> addFlash('success', "Votre article est créé. Il est en attente de validation.");

            return $this->redirectToRoute('article_index');
        }

        return $this->render('article/new.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/versions", name="article_versions",requirements={"id":"\d+"}, methods={"GET"})
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
     * @Route("/{id}/edit", name="article_edit", requirements={"id":"\d+"}, methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     */
    public function edit(Request $request, Article $article, MailerInterface $mailer, ArticleRepository $articleRepository): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Si la case article du mois est cochée, on décoche la case de l'ancien article du mois s'il existe
            if($article->getMonthlyArticle()){
                $monthlyArticleOld = $articleRepository->findOneBy(['monthly_article' => true]);
                if($monthlyArticleOld){
                    $monthlyArticleOld->setMonthlyArticle(false);
                }
            }

            $currentUser = $this->getUser();
            $article->setCreator($currentUser)
            ->setCreationDate(new \DateTime());

            // créer une nouvelle version
            $version = new Version();
            $version->setContent($form->get('content')->getData())
            ->setModificationDate(new \DateTime())
            ->setIsValidated(false)
            ->setContributor($currentUser)
            ->setArticle($article);
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

            $this -> addFlash('success', "Votre article est modifié. Il est en attente de validation.");

            return $this->redirectToRoute('article_index');
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/{version_id?current}", name="article_show", requirements={"id":"\d+"}, methods={"GET"})
     */
    public function show(Article $article, VersionRepository $versionRepository, String $version_id): Response
    {
        if ($version_id == "current") {
            $version = $versionRepository->find($article->getCurrentVersion());
        } else {
            $version = $versionRepository->find($version_id);
        }

        // Fetch all versions for a given article
        $versions = $versionRepository->findBy(['article' => $article->getId()], ['modification_date' => 'DESC']);
        // Extract the three last versions, to display in the article page
        $lastVersions = array_slice($versions, 0, 3);

        // Fetch the contributor's id of each version and
        // keep only the ones different to the article's author
        $contributors = [];
        foreach ($versions as $vs) {
            if ($article->getCreator()->getId() != $vs->getContributor()->getId()) {
                $contributors[] = $vs->getContributor();
            }
        }

        //Remove duplicated entries
        foreach ($contributors as $k=>$v) {
            if (($kt=array_search($v, $contributors))!==false and $k!=$kt) {
                unset($contributors[$kt]);
            }
        }

        
        return $this->render('article/show.html.twig', [
            'article' => $article,
            'version' => $version,
            'lastVersions' => $lastVersions,
            'contributors' => $contributors,
        ]);
    }

    /**
     * @Route("/{id}", name="article_delete", requirements={"id":"\d+"}, methods={"DELETE"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Article $article): Response
    {
        if ($this->isCsrfTokenValid('delete' . $article->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($article);
            $entityManager->flush();

            $this -> addFlash('erreur', "Votre article a été supprimé.");
        }

        return $this->redirectToRoute('article_index');
    }

    /**
        * @Route("/unvalidated_articles", name="unvalidated_articles", methods={"GET"})
        * @IsGranted("ROLE_MODERATOR")
        */
    public function unvalidatedArticles(VersionRepository $versionRepository, ArticleRepository $articleRepository, PaginatorInterface $paginator, Request $request) :Response
    {
        $currentVersions = [];
        $articles = $articleRepository->findAll();

        foreach ($articles as $article) {
            $idCurrentVersion = $article->getCurrentVersion();
            if (!empty($idCurrentVersion)) {
                $currentVersion = $versionRepository->find($idCurrentVersion);
                
                if ($currentVersion && !$currentVersion->getIsValidated()) {
                    $currentVersions[]=$currentVersion;
                }
            }
        }

        $currentVersionsPaginated = $paginator->paginate(
            $currentVersions, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            12 // Nombre de résultats par page
        );

        return $this->render('article/validate_articles.html.twig', [
            'currentVersions' => $currentVersionsPaginated
        ]);
    }
}
