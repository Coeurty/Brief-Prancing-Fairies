<?php

namespace App\Controller;

use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }



    #[Route('/comment/{id}/report', name: 'app_comment_report', methods: ['POST'])]
    public function report(Request $request, Comment $comment): Response
    {
        if ($this->isCsrfTokenValid('report' . $comment->getId(), $request->request->get('_token'))) {
            $comment->setIsReported(true);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_article_show', ['slug' => $comment->getArticle()->getSlug()]);
    }

    #[Route('/comment/{id}/delete', name: 'app_comment_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $comment->getId(), $request->request->get('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_article_show', [
            'slug' => $comment->getArticle()->getSlug()
        ]);
    }
}
