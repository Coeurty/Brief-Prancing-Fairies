<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\SliderImageRepository;
use App\Repository\TrackRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        ArticleRepository $articleRepository,
        SliderImageRepository $sliderImageRepository,
        TrackRepository $trackRepository
        ): Response
    {
        $sliderImages = $sliderImageRepository->findAll();
        // The track with the largest file size is the full track
        $EV5Track = $trackRepository->findBy([], ['fileSize' => 'DESC'], 1)[0];

        return $this->render('home/index.html.twig', [
            'sliderImages' => $sliderImages,
            'defaultTrack' => $EV5Track,
            'articles' => $articleRepository->findBy([], ['id' => 'DESC'], 6),
        ]);
    }
}
