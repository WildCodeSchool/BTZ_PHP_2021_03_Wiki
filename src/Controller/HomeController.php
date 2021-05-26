<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(ArticleRepository $articleRepository): Response
    {
        $lastArticles = $articleRepository->findLastArticles();
        $monthlyArticle = $articleRepository->findOneBy(['monthly_article' => true]);

        return $this->render('home/index.html.twig', [
            'lastArticles' => $lastArticles,
            'monthlyArticle' =>$monthlyArticle
        ]);
    }
}
