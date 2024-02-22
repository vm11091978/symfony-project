<?php

namespace App\Controller;

use App\Entity\Subcategory;
use App\Form\SubcategoryType;
use App\Repository\SubcategoryRepository;
use App\Repository\ArticleRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/subcategory')]
class SubcategoryController extends AbstractController
{
    #[Route('/list', name: 'app_subcategory_index', methods: ['GET'])]
    public function index(SubcategoryRepository $subcategoryRepository): Response
    {
        return $this->render('subcategory/index.html.twig', [
            'subcategories' => $subcategoryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_subcategory_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $subcategory = new Subcategory();
        $form = $this->createForm(SubcategoryType::class, $subcategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($subcategory);
            $entityManager->flush();

            $this->addFlash('message_status', 'The subcategory was successfully added.');

            return $this->redirectToRoute('app_subcategory_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('subcategory/new.html.twig', [
            'subcategory' => $subcategory,
            'form' => $form,
        ]);
    }
/*
    #[Route('/{id}', name: 'app_subcategory_show', methods: ['GET'])]
    public function show(Subcategory $subcategory): Response
    {
        return $this->render('subcategory/show.html.twig', [
            'subcategory' => $subcategory,
        ]);
    }
*/
    #[Route('/{id}/edit', name: 'app_subcategory_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Subcategory $subcategory,
        EntityManagerInterface $entityManager
    ): Response
    {
        $form = $this->createForm(SubcategoryType::class, $subcategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('message_status', 'Your changes have been saved successfully.');

            return $this->redirectToRoute('app_subcategory_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('subcategory/edit.html.twig', [
            'subcategory' => $subcategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_subcategory_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Subcategory $subcategory,
        ArticleRepository $articleRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        // Если удаляемая подкатегория содержит хотя бы одну статью, отменим удаление
        $subcategoryId = $request->get('id');
        if ($articleRepository->findBy(['subcategory_id' => $subcategoryId])) {
            $this->addFlash('subcategory_status', 'Error: Subcategory contains articles. '
                    . 'Delete the articles, or assign them to another subcategory, '
                    . 'before deleting this subcategory.');

            return $this->redirectToRoute('app_subcategory_index', [], Response::HTTP_SEE_OTHER);
        }

        if ($this->isCsrfTokenValid('delete'.$subcategory->getId(), $request->request->get('_token'))) {
            $entityManager->remove($subcategory);
            $entityManager->flush();
        }

        $this->addFlash('message_status', 'Subcategory deleted.');

        return $this->redirectToRoute('app_subcategory_index', [], Response::HTTP_SEE_OTHER);
    }
}
