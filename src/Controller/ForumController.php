<?php

namespace App\Controller;

use App\Entity\Topic;
use App\Entity\TopicCategory;
use App\Form\TopicCategoryType;
use App\Repository\TopicCategoryRepository;
use App\Repository\TopicRepository;
use App\Service\BreadcrumbService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/forum')]
final class ForumController extends AbstractController
{
    #[Route('/', name: 'app_forum_index', methods: ['GET'])]
    public function index(
        TopicCategoryRepository $topicCategoryRepository,
        FormFactoryInterface $formFactory,
        BreadcrumbService $breadcrumbService
    ): Response {
        $topicCategories = $topicCategoryRepository->findAll();
        $forms = [];

        foreach ($topicCategories as $topicCategory) {
            $forms[$topicCategory->getId()] = $formFactory
                ->createNamed("topic_category_{$topicCategory->getId()}", TopicCategoryType::class, $topicCategory)
                ->createView();
        }

        $newTopicCategoryForm = $this->createForm(TopicCategoryType::class, new TopicCategory());

        $breadcrumbs = $breadcrumbService->generateBreadcrumb([
            ['label' => 'Catégories', 'name' => null]
        ]);

        return $this->render('forum/index.html.twig', [
            'breadcrumbs' => $breadcrumbs,
            'topic_categories' => $topicCategories,
            'topic_category_forms' => $forms,
            'new_topic_category_form' => $newTopicCategoryForm->createView(),
        ]);
    }

    #[Route('/{slug}', name: 'app_forum_show_category', methods: ['GET'])]
    public function showCategory(
        string $slug,
        TopicCategoryRepository $topicCategoryRepository,
        FormFactoryInterface $formFactory,
        BreadcrumbService $breadcrumbService
    ): Response {
        $topicCategory = $topicCategoryRepository->findOneBySlug($slug);

        if (!$topicCategory) {
            throw $this->createNotFoundException("Catégorie non trouvée.");
        }

        $form = $formFactory
            ->createNamed("topic_category_{$topicCategory->getId()}", TopicCategoryType::class, $topicCategory)
            ->createView();

        $breadcrumbs = $breadcrumbService->generateBreadcrumb([
            ['label' => 'Catégories', 'name' => 'app_forum_index'],
            ['label' => $topicCategory->getName(), 'name' => null]
        ]);

        return $this->render('forum/show_category.html.twig', [
            'breadcrumbs' => $breadcrumbs,
            'topic_category' => $topicCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{categorySlug}/{topicSlug}', name: 'app_forum_show_topic', methods: ['GET'])]
    public function showTopic(
        string $categorySlug,
        string $topicSlug,
        TopicCategoryRepository $topicCategoryRepository,
        TopicRepository $topicRepository,
        BreadcrumbService $breadcrumbService
    ): Response {
        $topicCategory = $topicCategoryRepository->findOneBySlug($categorySlug);
        if (!$topicCategory) {
            throw $this->createNotFoundException("Catégorie non trouvée.");
        }

        $topic = $topicRepository->findOneBy(["slug" => $topicSlug]);
        if (!$topic) {
            throw $this->createNotFoundException("Sujet non trouvé.");
        }

        $breadcrumbs = $breadcrumbService->generateBreadcrumb([
            ['label' => 'Catégories', 'name' => 'app_forum_index'],
            ['label' => $topicCategory->getName(), 'name' => 'app_forum_show_category', 'params' => ['slug' => $categorySlug]],
            ['label' => $topic->getTitle(), 'name' => null]
        ]);

        return $this->render('forum/show_topic.html.twig', [
            'breadcrumbs' => $breadcrumbs,
            'topic' => $topic,
        ]);
    }
}
