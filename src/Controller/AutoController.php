<?php

namespace App\Controller;

use App\Entity\Auto;
use App\Form\AutoType;
use App\Repository\AutoRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/auto')]
class AutoController extends AbstractController
{
    #[Route('/', name: 'app_auto_index', methods: ['GET'])]
    public function index(AutoRepository $autoRepository): Response
    {
        return $this->render('auto/index.html.twig', [
            'autos' => $autoRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_auto_new', methods: ['GET', 'POST'])]
    public function new(Request $request, AutoRepository $autoRepository, FlashyNotifier $flashyNotifier): Response
    {
        $auto = new Auto();
        $form = $this->createForm(AutoType::class, $auto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $autoRepository->save($auto, true);
            $this->addFlash('success', 'Success! Auto toegevoegd!');

            return $this->redirectToRoute('app_auto_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('auto/new.html.twig', [
            'auto' => $auto,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_auto_show', methods: ['GET'])]
    public function show(Auto $auto): Response
    {
        return $this->render('auto/show.html.twig', [
            'auto' => $auto,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_auto_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Auto $auto, AutoRepository $autoRepository): Response
    {
        $form = $this->createForm(AutoType::class, $auto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $autoRepository->save($auto, true);

            return $this->redirectToRoute('app_auto_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('auto/edit.html.twig', [
            'auto' => $auto,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_auto_delete', methods: ['POST'])]
    public function delete(Request $request, Auto $auto, AutoRepository $autoRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$auto->getId(), $request->request->get('_token'))) {
            $autoRepository->remove($auto, true);
        }

        return $this->redirectToRoute('app_auto_index', [], Response::HTTP_SEE_OTHER);
    }
}
