<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Repository\SubcategoryRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/article')]
class ArticleController extends AbstractController
{
    public function __construct(CategoryRepository $catRep, SubcategoryRepository $subRep)
    {
        // Получим ассоциативный массив всех имеющихся категорий
        $this->categories = $catRep->findAllAssignId();
        // А так же ассоциативный массив всех имеющихся подкатегорий
        $this->subcategories = $subRep->findAllAssignId();
    }

    #[Route('/list', name: 'app_article_index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findAllJoinAuthors();

        return $this->render('article/index.html.twig', [
            //'articles' => $articleRepository->findAll(),
            'articles' => $articles,
            'categories' => $this->categories,
            'subcategories' => $this->subcategories,
        ]);
    }

    #[Route('/new', name: 'app_article_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $articleCategoryOrSubcategory = $request->get('article_subcategory');
            /*
             * Если переданное из представления значение articleCategoryOrSubcategory является числом,
             * значит статья принадлежит непосредственно какой-то категории минуя подкатегорию,
             * запишем это значение в БД в столбец "categoryId",
             * а значение subcategoryId для этой статьи будет отсутствовать.
             * Иначе эта статья принадлежит какой-то подкатегории, запишем это значение в БД в столбец "subcategoryId",
             * а значение categoryId для этой статьи будет равно нулю.
             */
            if (is_numeric($articleCategoryOrSubcategory)) {
                // если статья не относится ни к какой категории или подкатегории, то $articleCategoryOrSubcategory = null
                $article->setCategoryId($articleCategoryOrSubcategory);
            } elseif ($articleCategoryOrSubcategory != null) {
                $article->setSubcategoryId(str_replace('sub_', '', $articleCategoryOrSubcategory));
            }
            $entityManager->persist($article);
            $entityManager->flush();

            $this->addFlash('message_status', 'The article was successfully added.');

            return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('article/new.html.twig', [
            'article' => $article,
            'categories' => $this->categories,
            'subcategories' => $this->subcategories,
            'form' => $form,
        ]);
    }
/*
    #[Route('/{id}', name: 'app_article_show', methods: ['GET'])]
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }
*/
    #[Route('/{id}/edit', name: 'app_article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $viewArticles = $entityManager->getRepository(Article::class)->find($article->getId());
        
            $articleCategoryOrSubcategory = $request->get('article_subcategory');
            /*
             * Если переданное из представления значение articleCategoryOrSubcategory является числом,
             * значит статья принадлежит непосредственно какой-то категории минуя подкатегорию,
             * запишем это значение в БД в столбец "categoryId",
             * а значение subcategoryId для этой статьи будет отсутствовать.
             * Иначе эта статья принадлежит какой-то подкатегории, запишем это значение в БД в столбец "subcategoryId",
             * а значение categoryId для этой статьи будет равно нулю.
             */
            if (is_numeric($articleCategoryOrSubcategory)) {
            	// если статья не относится ни к какой категории или подкатегории, то $articleCategoryOrSubcategory = null
                $viewArticles->setCategoryId($articleCategoryOrSubcategory);
            } elseif ($articleCategoryOrSubcategory != null) {
                $viewArticles->setSubcategoryId(str_replace('sub_', '', $articleCategoryOrSubcategory));
            }
            $entityManager->flush();
            
            $this->addFlash('message_status', 'Your changes have been saved successfully.');

            return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'categories' => $this->categories,
            'subcategories' => $this->subcategories,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_article_delete', methods: ['POST'])]
    public function delete(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();
        }

        $this->addFlash('message_status', 'Article deleted.');

        return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
    }
}
