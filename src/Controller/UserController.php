<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use App\Repository\ArticleRepository;

final class UserController extends AbstractController
{
    #[Route('/mon-compte', name: 'app_user')]
    public function index(ArticleRepository $articleRepository): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }
        $articles = $articleRepository->findUserArticles($user);

        return $this->render('user/index.html.twig', [
            'user' => $user,
            'articles' => $articles
        ]);
    }
}
