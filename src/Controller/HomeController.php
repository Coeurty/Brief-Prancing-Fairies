<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\SliderImageRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        ArticleRepository $articleRepository,
        SliderImageRepository $sliderImageRepository
        ): Response
    {
        $sliderImages = $sliderImageRepository->findAll();

        return $this->render('home/index.html.twig', [
            'sliderImages' => $sliderImages,
            'articles' => $articleRepository->findBy([], ['id' => 'DESC'], 6),
        ]);
    }
}
