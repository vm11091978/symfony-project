<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use App\Repository\SubcategoryRepository;
use App\Repository\ArticleRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/category')]
class CategoryController extends AbstractController
{
    #[Route('/list', name: 'app_category_index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('message_status', 'The category was successfully added.');

            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('category/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }
/*
    #[Route('/{id}', name: 'app_category_show', methods: ['GET'])]
    public function show(Category $category): Response
    {
        return $this->render('category/show.html.twig', [
            'category' => $category,
        ]);
    }
*/
    #[Route('/{id}/edit', name: 'app_category_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Category $category,
        EntityManagerInterface $entityManager
    ): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('message_status', 'Your changes have been saved successfully.');

            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_category_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Category $category,
        ArticleRepository $articleRepository,
        SubcategoryRepository $subcategoryRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        // Если удаляемая категория непосредственно содержит хотя бы одну статью, отменим удаление
        $categoryId = $request->get('id');
        $flag = false;
        if ($articleRepository->findBy(['category_id' => $categoryId])) {
            $this->addFlash('message_error', 'Category contains articles. '
                    . 'Delete the articles, or assign them to another category, '
                    . 'before deleting this category.');
            $flag = true;
        }
/*
        // Если удаляемая категория содержит хотя бы одну подкатегорию, отменим удаление
        if ($subcategoryRepository->findBy(['category' => $categoryId])) {
            $this->addFlash('message_error', 'Category contains subcategories. '
                    . 'Delete the subcategories, or assign them to another category, '
                    . 'before deleting this category.');
            $flag = true;
        }
*/
        // Получим массив всех ID подкатегорий для данной категории
        $subcategoriesId = array();
        $subcategories = $subcategoryRepository->findByCategory($categoryId);
        foreach ($subcategories as $subcategory) {
            $subcategoriesId[] = $subcategory->getId();
            
        }

        // Если удаляемая категория содержит хотя бы одну статью в подкатегориях, отменим удаление
        if ($articleRepository->findBy(['subcategory_id' => $subcategoriesId])) {
            $this->addFlash('message_error', 'Category contains articles in subcategories. '
                    . 'Delete the articles, or assign them to another category, '
                    . 'before deleting this category.');
            $flag = true;
        }
 
        // Если удаляемая категория пуста, приступим к её удалению
        if (! $flag) {
            if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
                $entityManager->remove($category);
                $entityManager->flush();
            }

            $this->addFlash('message_status', 'Category deleted.');
        }

        return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
