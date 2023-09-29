<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\ProjectionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectionController extends AbstractController
{
    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    #[Route('/ajout_projection', name: 'app_ajout_projection')]
    public function ajoutProjection(Request $request): Response
    {
        $form = $this->createForm(ProjectionType::class);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
        
            if ($form->isSubmitted() && $form->isValid()) {
                $projection = $form->getData();
                $this-> manager->persist($projection);
                $this-> manager->flush();

        
                return $this->redirectToRoute('app_home');
            }
        }

        return $this->render('projection/ajout_projection.html.twig', [
            'controller_name' => 'HomeController',
            'form' => $form
        ]);
    }
}
