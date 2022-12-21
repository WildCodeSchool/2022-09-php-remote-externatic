<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Form\AnnonceType;
use App\Repository\AnnonceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

#[route('/search', name: "search_")]
class AnnonceController extends AbstractController
{
    #[Route('/results', name: 'results')]
    public function index(Request $request, AnnonceRepository $annonceRepository): Response
    {
        $fetchedAnnonces = is_null($request->get('form')['searchQuery']) ?
            $annonceRepository->annonceFinder("*") :
            $annonceRepository->annonceFinder($request->get('form')['searchQuery']);
        return $this->render('annonce/results.html.twig', [
            'fetchedAnnonces' => $fetchedAnnonces,
        ]);
    }


    #[Route('/new', name: 'annonce_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AnnonceRepository $annonceRepository): Response
    {
        $annonce = new Annonce();
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $annonceRepository->save($annonce, true);
            $this->addFlash('success', 'Annonce en ligne');
        }
        return $this->renderForm('annonce/_form.html.twig', [
            'form' => $form,
            'annonce' => $annonce]);
    }
    #[Route('/{id}', name: 'show')]
    public function show(Annonce $annonce): response
    {
        return $this->render('annonce/show.html.twig', [
            'annonce' => $annonce,
        ]);
    }
}
