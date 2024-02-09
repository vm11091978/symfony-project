<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 *
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * @return Article[] Returns an array of Article objects
     */
    public function findByActiveJoinAuthors($categoryId = false, $subcategoryId = false, $count = MAX_NUM_ARTICLES): array
    {
        if ($categoryId) {
            // Если пришла переменная categoryId, то переменная subcategoryId представляет собой массив из Id подкатегорий,
            // относящихся к категории categoryId. Массив может быть пустым, если эта категория не содержит в себе подкатегорий.
            // print_r($subcategoryId);
            $viewArticles = $this->createQueryBuilder('a')
                ->andWhere('a.active = true')
                ->andWhere('a.category_id = :val')
                ->orWhere('a.subcategory_id IN(:val2)')
                ->setParameter('val', $categoryId)
                ->setParameter('val2', $subcategoryId)
                ->orderBy('a.publicationDate', 'DESC')
                ->orderBy('a.id', 'DESC')
                ->setMaxResults(MAX_NUM_ARTICLES)
                ->getQuery()
                ->getResult()
            ;
    	}
    	elseif ($subcategoryId) {
            $viewArticles = $this->findBy(
                ['active' => true, 'subcategory_id' => $subcategoryId],
                ['publicationDate' => 'DESC', 'id' => 'DESC'],
                MAX_NUM_ARTICLES
            );
        }
        else {
            $viewArticles = $this->findBy(
                ['active' => true],
                ['publicationDate' => 'DESC', 'id' => 'DESC'],
                $count
            );
        }

        $articles = array();
        foreach ($viewArticles as $article) {
            if ($users = $article->getUsers()) {
                $authors = array();
                foreach ($users as $user) {
                    $authors[] = $user->getLogin();
                }
                $article->authors = $authors;
            }
            $articles[] = $article;
        }

        return $articles;
    }

    /**
     * @return Article
     */
    public function findOneByIdJoinAuthors($articleId): ?Article
    {
        $article = $this->findOneById($articleId);

        if ($article && $users = $article->getUsers()) {
            $authors = array();
            foreach ($users as $user) {
                $authors[] = $user->getLogin();
            }
            $article->authors = $authors;
        }

        return $article;
    }
}
