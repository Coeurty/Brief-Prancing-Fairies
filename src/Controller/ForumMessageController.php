<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/forum/message')]
final class ForumMessageController extends AbstractController
{
    #[Route('/new', name: 'app_forum_message_new', methods: ['POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response {
        $this->denyAccessUnlessGranted("ROLE_USER");

        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setCreatedAt(new \DateTimeImmutable());
            $message->setUpdatedAt(new \DateTimeImmutable());
            $message->setIp($request->getClientIp());
            $message->setUser($this->getUser());

            $entityManager->persist($message);
            $entityManager->flush();

            $topic = $message->getTopic();
            return $this->redirectToRoute('app_forum_show_topic', [
                'categorySlug' => $topic->getCategory()->getSlug(),
                'topicSlug' => $topic->getSlug(),
            ]);
        }

        return $this->redirectToRoute('app_forum_index');
    }

    // TODO: Handle editing and deleting messages
    // #[Route('/{id}/edit', name: 'app_forum_message_edit', methods: ['GET', 'POST'])]
    // public function edit(Request $request, Message $message, EntityManagerInterface $entityManager): Response
    // {
    //     // TODO: Add authorization check (author or moderator)
    //     $this->denyAccessUnlessGranted("ROLE_USER");
    //     $form = $this->createForm(MessageType::class, $message);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $entityManager->flush();

    //         return $this->redirectToRoute('app_forum_message_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->render('forum_message/edit.html.twig', [
    //         'message' => $message,
    //         'form' => $form,
    //     ]);
    // }

    // #[Route('/{id}', name: 'app_forum_message_delete', methods: ['POST'])]
    // public function delete(Request $request, Message $message, EntityManagerInterface $entityManager): Response
    // {
    //     // TODO: Add authorization check (author or moderator)
    //     $this->denyAccessUnlessGranted("ROLE_USER");
    //     if ($this->isCsrfTokenValid('delete' . $message->getId(), $request->getPayload()->getString('_token'))) {
    //         $entityManager->remove($message);
    //         $entityManager->flush();
    //     }

    //     return $this->redirectToRoute('app_forum_message_index', [], Response::HTTP_SEE_OTHER);
    // }
}
