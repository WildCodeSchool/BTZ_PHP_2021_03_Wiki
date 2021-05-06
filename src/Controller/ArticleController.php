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

/**
 * @Route("/article")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="article_index", methods={"GET"})
     */
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->findAll(),
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
            $entityManager->flush();

            $article->setCurrentVersion($version->getId());

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
     * @Route("/{id}", name="article_show", methods={"GET"})
     */
    public function show(Article $article, VersionRepository $versionRepository): Response
    {
        $currentVersion = $versionRepository->find($article->getCurrentVersion());
        $lastVersions = $versionRepository->findBy(['article' => $article->getId()], ['modification_date' => 'DESC'], 3);
        return $this->render('article/show.html.twig', [
            'article' => $article,
            'currentVersion' => $currentVersion,
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
            $this->getDoctrine()->getManager()->flush();

            $email = (new Email())

                ->from('from@example.com')

                ->to('to@example.com')

                ->subject('Une article vient d\'être modifiée !')

                ->html('<p>Une article vient d\'être modifiée sur Wiki !</p>');


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
