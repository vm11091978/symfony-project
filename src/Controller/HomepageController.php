<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Repository\SubcategoryRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

define("HOMEPAGE_NUM_ARTICLES", 5);

class HomepageController extends AbstractController
{
    public function __construct(CategoryRepository $catRep, SubcategoryRepository $subRep)
    {
        // Получим ассоциативный массив всех имеющихся категорий
        $this->categories = $catRep->findAllAssignId();
        // А так же ассоциативный массив всех имеющихся подкатегорий
        $this->subcategories = $subRep->findAllAssignId();
    }

    /**
     * Вывод домашней ("главной") страницы сайта
     */
    #[Route('/', name: 'app_homepage', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findByActiveJoinAuthors(false, false, HOMEPAGE_NUM_ARTICLES);

        return $this->render('homepage/index.html.twig', [
            'pageTitle' => "Простая CMS на PHP",
            'pageHeading' => "Домашняя страница",
            'articles' => $articles,
            'categories' => $this->categories,
            'subcategories' => $this->subcategories,
        ]);
    }

    /**
     * Вывод одного из вариантов архивной страницы сайта
     * Клиент захотел посмотреть все статьи - покажем ему все активные статьи
     */
    #[Route('/archive', name: 'app_archive', methods: ['GET'])]
    public function archive(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findByActiveJoinAuthors();
        $pageHeading = "Article Archive";

        return $this->render('homepage/archive.html.twig', [
            'pageTitle' => $pageHeading . " | Widget News",
            'pageHeading' => $pageHeading,
            'articles' => $articles,
            'categories' => $this->categories,
            'subcategories' => $this->subcategories,
        ]);
    }
    
    /**
     * Вывод одного из вариантов архивной страницы сайта
     * Клиент захотел посмотреть все статьи из какой-то конкретной категории
     */
    #[Route('/archive/category/{id}', name: 'app_category', methods: ['GET'])]
    public function showByCategory(
        int $id,
        ArticleRepository $articleRepository,
        CategoryRepository $categoryRepository,
        SubcategoryRepository $subcategoryRepository
    ): Response
    {
        // Если URL-ссылка ведёт на страницу с несуществующим Id категории
        if (! $category = $categoryRepository->find($id)) {
            return $this->render('exception/error.html.twig', [
                'errorMessage' => "Категория с id = $id не найдена"
            ]);
        }

        // Получим массив всех ID подкатегорий для данной категории
        $subcategoriesId = $subcategoryRepository->findByCategoryGetSubcategoriesId($id);

        $articles = $articleRepository->findByActiveJoinAuthors($id, $subcategoriesId);

        return $this->render('homepage/archive.html.twig', [
            'pageTitle' => $category->getName() . " | Widget News",
            'pageHeading' => $category->getName(),
            'description' => $category->getDescription() ?? null,
            'articles' => $articles,
            'categories' => $this->categories,
            'subcategories' => $this->subcategories,
        ]);
    }

    /**
     * Вывод одного из вариантов архивной страницы сайта
     * Клиент захотел посмотреть все статьи из какой-то конкретной подкатегории
     */
    #[Route('/archive/subcategory/{id}', name: 'app_subcategory', methods: ['GET'])]
    public function showBySubcategory(
        int $id,
        ArticleRepository $articleRepository,
        SubcategoryRepository $subcategoryRepository
    ): Response
    {
        // Если URL-ссылка ведёт на страницу с несуществующим Id подкатегории
        if (! $subcategory = $subcategoryRepository->find($id)) {
            return $this->render('exception/error.html.twig', [
                'errorMessage' => "Подкатегория с id = $id не найдена"
            ]);
        }

        $articles = $articleRepository->findByActiveJoinAuthors(false, $id);

        return $this->render('homepage/archive.html.twig', [
            'pageTitle' => $subcategory->getSubname() . " | Widget News",
            'pageHeading' => $subcategory->getSubname(),
            'articles' => $articles,
            'subcategories' => $this->subcategories,
        ]);
    }

    /**
     * Вывод страницы с конкретной статьёй
     */
    #[Route('/viewarticle/{id}', name: 'app_article', methods: ['GET'])]
    public function viewArticle(
        int $id,
        ArticleRepository $articleRepository,
        CategoryRepository $categoryRepository,
        SubcategoryRepository $subcategoryRepository
    ): Response
    {
        // $articleId = $request->attributes->get('id');

        // Если URL-ссылка ведёт на страницу с несуществующим Id статьи
        if (! $article = $articleRepository->findOneByIdJoinAuthors($id)) {
            return $this->render('exception/error.html.twig', [
                'errorMessage' => "Статья с id = $id не найдена"
            ]);
        }

        // Если статья относится к какой-нибудь категории и/или подкатегории, получим инфу о ней/о них
        if ($categoryId = $article->getCategoryId()) {
            $category = $categoryRepository->find($categoryId);
        }
        if ($subcategoryId = $article->getSubcategoryId()) {
            $subcategory = $subcategoryRepository->find($subcategoryId);
            $category = $subcategory->getCategory();
        }

        return $this->render('homepage/show_item.html.twig', [
            'pageTitle' => $article->getTitle() . " | Простая CMS",
            'pageHeading' => $article->getTitle(),
            'article' => $article,
            'category' => $category ?? null,
            'subcategory' => $subcategory ?? null,
        ]);
    }

    /**
     * Если не передан id категории, подкатегории или статьи там, где он ожидался,
     * отправим клиента на страницу архива
     */
    #[Route('/archive/category'), Route('/archive/subcategory'), Route('/article')]
    public function redirectToArchive() {
        return $this->redirectToRoute('app_archive', [], Response::HTTP_SEE_OTHER);
    }
}
