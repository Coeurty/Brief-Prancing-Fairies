<?php

namespace App\Controller;

use App\Entity\TopicCategory;
use App\Form\TopicCategoryType;
use App\Repository\TopicCategoryRepository;
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
        FormFactoryInterface $formFactory
    ): Response {
        $topicCategories = $topicCategoryRepository->findAll();
        $forms = [];

        foreach ($topicCategories as $topicCategory) {
            $forms[$topicCategory->getId()] = $formFactory
                ->createNamed("topic_category_{$topicCategory->getId()}", TopicCategoryType::class, $topicCategory)
                ->createView();
        }

        $newTopicCategoryForm = $this->createForm(TopicCategoryType::class, new TopicCategory());

        return $this->render('forum/index.html.twig', [
            'topic_categories' => $topicCategories,
            'topic_category_forms' => $forms,
            'new_topic_category_form' => $newTopicCategoryForm->createView(),
        ]);
    }

    #[Route('/{slug}', name: 'app_forum_show_category', methods: ['GET'])]
    public function show(
        string $slug,
        TopicCategoryRepository $topicCategoryRepository,
        FormFactoryInterface $formFactory
    ): Response {
        $topicCategory = $topicCategoryRepository->findOneBySlug($slug);

        if (!$topicCategory) {
            throw $this->createNotFoundException("Catégorie non trouvée.");
        }

        $form = $formFactory
            ->createNamed("topic_category_{$topicCategory->getId()}", TopicCategoryType::class, $topicCategory)
            ->createView();

        return $this->render('forum/show_category.html.twig', [
            'topic_category' => $topicCategory,
            'form' => $form,
        ]);
    }
}
