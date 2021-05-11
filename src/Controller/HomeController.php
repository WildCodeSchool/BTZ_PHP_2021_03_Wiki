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
        $allArticles = $articleRepository->findAll();
        $lastArticles = array_slice($allArticles, 0, 6);

        return $this->render('home/index.html.twig', [
            'lastArticles' => $lastArticles,
        ]);
    }
}
