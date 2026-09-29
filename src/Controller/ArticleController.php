<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Service\FileUploader;

final class ArticleController extends AbstractController
{
    #[Route('/article/{id}', name: 'app_article')]
    public function index(ArticleRepository $articleRepository, $id): Response
    {
        $article = $articleRepository->find($id);

        return $this->render('article/index.html.twig', [
            'article' => $article,
            'isEdit' => true
        ]);
    }

    #[Route('/ajouter-article', name: 'add_article')]
    public function addArticle(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        $article = new Article();
        $form = $this->createForm(ArticleFormType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $image */
            $image = $form->get('image')->getData();
            if ($image) {
                $image = $fileUploader->upload($image);
                $article->setImage($image);
            }
            $article->setUser($user);

            $entityManager->persist($article);
            $entityManager->flush();


            return $this->redirectToRoute('app_user');
        }

        return $this->render('article/addArticle.html.twig', [
            'articleForm' => $form,
            'isEdit' => true
        ]);
    }

    /**
     * Action dédiée à la modification d'un article existant.
     */
    #[Route('/modifier-article/{id}', name: 'edit_article')]
    public function editArticle(Article $article, Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($article->getUser() !== $user) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(ArticleFormType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();

            if ($image) {
                $oldImage = $article->getImage();

                $newFilename = $fileUploader->upload($image);

                $article->setImage($newFilename);

                if ($oldImage) {
                    $fileUploader->remove($oldImage);
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_user');
        }

        return $this->render('article/addArticle.html.twig', [
            'articleForm' => $form,
            'isEdit' => true,
            'article' => $article,
        ]);
    }
}
