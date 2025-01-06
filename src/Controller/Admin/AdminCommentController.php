<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin')]
class AdminCommentController extends AbstractController
{
    #[Route('/comments/reported', name: 'admin_reported_comments')]
    public function reportedComments(CommentRepository $commentRepository): Response
    {
        $reportedComments = $commentRepository->findBy(['isReported' => true]);

        return $this->render('admin/reported_comments.html.twig', [
            'comments' => $reportedComments
        ]);
    }

    #[Route('/comments/{id}/approve', name: 'admin_approve_comment', methods: ['POST'])]
    public function approveComment(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('approve'.$comment->getId(), $request->request->get('_token'))) {
            $comment->setIsReported(false);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_reported_comments');
    }
}