<?php

namespace App\Controller;

use App\Repository\TrackRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ItineraryController extends AbstractController
{
    #[Route('/itineraire', name: 'app_itinerary')]
    public function index(TrackRepository $trackRepository): Response
    {
        $tracks = $trackRepository->findBy([], ['displayOrder' => 'ASC']);
        // The track with the largest file size is the full track
        $EV5Track = $trackRepository->findLargestTrackFile();

        return $this->render('itinerary/index.html.twig', [
            'tracks' => $tracks,
            'defaultTrack' => $EV5Track,
        ]);
    }
}
