<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PagesController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->lastArticles();
        
        return $this->render('pages/index.html.twig', [
            'articles' => $articles,
            'isEdit' => true
        ]);
    }
}
