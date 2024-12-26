<?php

namespace App\Controller;

use App\Entity\TopicCategory;
use App\Form\TopicCategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/topic/category')]
final class TopicCategoryController extends AbstractController
{
    #[Route('/new', name: 'app_topic_category_new', methods: ['POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
    ): Response {
        // $this->denyAccessUnlessGranted("ROLE_MODERATOR");
        $topicCategory = new TopicCategory();
        $form = $this->createForm(TopicCategoryType::class, $topicCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categorySlug = $slugger->slug($topicCategory->getName())->lower();
            $topicCategory->setSlug($categorySlug);

            $entityManager->persist($topicCategory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_forum_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/edit', name: 'app_topic_category_edit', methods: ['POST'])]
    public function edit(
        Request $request,
        TopicCategory $topicCategory,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
        FormFactoryInterface $formFactory
    ): Response {
        // $this->denyAccessUnlessGranted("ROLE_MODERATOR");
        $form = $formFactory->createNamed("topic_category_{$topicCategory->getId()}", TopicCategoryType::class, $topicCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categorySlug = $slugger->slug($topicCategory->getName())->lower();
            $topicCategory->setSlug($categorySlug);

            $entityManager->persist($topicCategory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_forum_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/{id}', name: 'app_topic_category_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        TopicCategory $topicCategory,
        EntityManagerInterface $entityManager
    ): Response {
        // $this->denyAccessUnlessGranted("ROLE_MODERATOR");

        if ($this->isCsrfTokenValid('delete' . $topicCategory->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($topicCategory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_forum_index', [], Response::HTTP_SEE_OTHER);
    }
}
