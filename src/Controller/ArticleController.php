<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Service\PaginationService;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ArticleController extends AbstractController
{

    private PaginationService $paginationService;

    public function __construct(PaginationService $paginationService)
    {
        $this->paginationService = $paginationService;
    }

    #[Route('/actualité', name: 'app_article_index')]
    public function index(Request $request): Response
    {
        $paginatedArticle = $this->paginationService->paginate($request);

        return $this->render('article/index.html.twig', [
            'articles' => $paginatedArticle,
        ]);
    }

    #[Route('/article/new', name: 'app_article_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_MODERATOR')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $article = new Article();
        $article->setCreatedAt(new \DateTimeImmutable());
        $article->setUpdatedAt(new \DateTimeImmutable());
        $article->setUser($this->getUser());
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('article/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/article/{slug}', name: 'app_article_show', methods: ['GET', 'POST'])]
    public function show(Request $request, string $slug, ArticleRepository $articleRepository, EntityManagerInterface $entityManager): Response
    {

        $article = $articleRepository->findOneBy(['slug' => $slug]);

        if (!$article) {
            throw $this->createNotFoundException('Article non trouvé');
        }

        $comment = new Comment();
        $comment->setCreatedAt(new \DateTimeImmutable());
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setArticle($article);
            $comment->setUser($this->getUser());


            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('app_article_show', [
                'slug' => $article->getSlug()
            ]);
        }

        return $this->render('article/show.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/article/{slug}/edit', name: 'app_article_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_MODERATOR')]
    public function edit(Request $request, EntityManagerInterface $entityManager, string $slug, ArticleRepository $articleRepository): Response
    {
        $article = $articleRepository->findOneBy(['slug' => $slug]);

        if (!$article) {
            throw $this->createNotFoundException('Article non trouvé');
        }

        $form = $this->createForm(ArticleType::class, $article);
        $article->setUpdatedAt(new \DateTimeImmutable());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/article/{slug}/delete', name: 'app_article_delete', methods: ['POST'])]
    #[IsGranted('ROLE_MODERATOR')]
    public function delete(Request $request, string $slug, ArticleRepository $articleRepository, EntityManagerInterface $entityManager): Response
    {
        $article = $articleRepository->findOneBy(['slug' => $slug]);

        if (!$article) {
            throw $this->createNotFoundException('Article non trouvé');
        }

        if ($this->isCsrfTokenValid('delete' . $article->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
    }
}
