<?php

namespace App\Repository;

use App\Entity\Subcategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Subcategory>
 *
 * @method Subcategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subcategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subcategory[]    findAll()
 * @method Subcategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubcategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subcategory::class);
    }

    /**
     * @return Subcategory[] Returns an array of Subcategory objects
     */
    public function findAllAssignId(): array
    {
        $viewSubcategories = $this->findAll();

        // Переберём массив и сделаем ключём каждой подкатегории ID этой подкатегории
        $subcategories = array();
        foreach ($viewSubcategories as $subcategory) {
            $subcategories[$subcategory->getId()] = $subcategory;
        }

        return $subcategories;
    }

    /**
     * @return SubcategoryId[] Returns an array of Subcategory id of numbers
     */
    public function findByCategoryGetSubcategoriesId($categoryId): array
    {
        // Получим массив всех ID подкатегорий для данной категории
        $subcategories = $this->findByCategory($categoryId);
        $subcategoriesId = array();
        foreach ($subcategories as $subcategory) {
            $subcategoriesId[] = $subcategory->getId();
        }

        return $subcategoriesId;
    }
}
