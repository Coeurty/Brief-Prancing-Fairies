<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Topic;
use App\Form\TopicType;
use App\Repository\TopicCategoryRepository;
use App\Service\BreadcrumbService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/topic')]
final class TopicController extends AbstractController
{
    #[Route('/new', name: 'app_topic_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
        TopicCategoryRepository $topicCategoryRepository,
        BreadcrumbService $breadcrumbService
    ): Response {
        $this->denyAccessUnlessGranted("ROLE_USER");
        $topic = new Topic();

        $categorySlug = $request->query->get('categorie');
        $topicCategory = null;
        if ($categorySlug) {
            $topicCategory = $topicCategoryRepository->findOneBySlug($categorySlug);
        }
        $topic->setCategory($topicCategory);

        $form = $this->createForm(TopicType::class, $topic, [
            'is_new_topic' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $topic->setCreatedAt(new \DateTimeImmutable());

            $topicSlug = $slugger->slug($topic->getTitle())->lower();
            $topic->setSlug($topicSlug);

            $topic->setUser($this->getUser());

            $messageContent = $form->get('message')->getData();
            if ($messageContent) {
                $message = new Message();
                $message->setContent($messageContent);
                $message->setCreatedAt(new \DateTimeImmutable());
                $message->setTopic($topic);
                $message->setUser($this->getUser());

                $entityManager->persist($message);
            }

            $entityManager->persist($topic);
            $entityManager->flush();

            return $this->redirectToRoute(
                'app_forum_show_topic',
                [
                    'categorySlug' => $topic->getCategory()?->getSlug(),
                    'topicSlug' => $topic->getSlug()
                ],
                Response::HTTP_SEE_OTHER
            );
        }

        $breadcrumbs = $breadcrumbService->generateBreadcrumb([
            ['label' => 'Catégories', 'name' => 'app_forum_index'],
            ['label' => 'Nouveau sujet', 'name' => null]
        ]);

        return $this->render('topic/new.html.twig', [
            'breadcrumbs' => $breadcrumbs,
            'topic' => $topic,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_topic_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Topic $topic,
        EntityManagerInterface $entityManager,
        BreadcrumbService $breadcrumbService
    ): Response {
        $form = $this->createForm(TopicType::class, $topic);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_forum_index', [], Response::HTTP_SEE_OTHER);
        }

        $breadcrumbs = $breadcrumbService->generateBreadcrumb([
            ['label' => 'Catégories', 'name' => 'app_forum_index'],
            [
                'label' => $topic->getCategory()->getName(),
                'name' => 'app_forum_show_category',
                'params' => ['slug' => $topic->getCategory()->getSlug()]
            ],
            [
                'label' => $topic->getTitle(),
                'name' => 'app_forum_show_topic',
                'params' => [
                    'categorySlug' => $topic->getCategory()->getSlug(),
                    'topicSlug' => $topic->getSlug()
                ],
            ],
            ['label' => 'Modification', 'name' => null]
        ]);

        return $this->render('topic/edit.html.twig', [
            'breadcrumbs' => $breadcrumbs,
            'topic' => $topic,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_topic_delete', methods: ['POST'])]
    public function delete(Request $request, Topic $topic, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $topic->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($topic);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_forum_show_category', ['slug' => $topic->getCategory()->getSlug()], Response::HTTP_SEE_OTHER);
    }
}
