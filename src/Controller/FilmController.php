<?php

namespace App\Controller;

use App\Repository\FilmRepository;
use App\Repository\ProjectionRepository;
use App\Form\FilmType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FilmController extends AbstractController
{

    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    
    #[Route('/info_film/{id}', name: 'app_info_film')]
    public function consulterInfoFilm(int $id, FilmRepository $filmRepository, ProjectionRepository $projectionRepository): Response
    {

        $film = $filmRepository->find($id);
        $projections = $projectionRepository->findBy(['id_film' => $id]);

        $currentDate = new \DateTime();

        $projectionsFutures = array_filter($projections, function($projection) use ($currentDate) {
            return $projection->getDebut() > $currentDate;
        });

        return $this->render('film/info_film.html.twig', [
            'controller_name' => 'HomeController',
            'film' => $film,
            'projections' => $projectionsFutures
        ]);
    }

    #[Route('/ajout_film', name: 'app_ajout_film')]
    public function ajoutFilm(Request $request): Response
    {
        $form = $this->createForm(FilmType::class);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
        
            if ($form->isSubmitted() && $form->isValid()) {
                $film = $form->getData();
                $this-> manager->persist($film);
                $this-> manager->flush();

        
                return $this->redirectToRoute('app_home');
            }
        }

        return $this->render('film/ajout_film.html.twig', [
            'controller_name' => 'HomeController',
            'form' => $form
        ]);
    }
}
