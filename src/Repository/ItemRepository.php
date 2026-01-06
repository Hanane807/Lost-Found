<?php

namespace App\Repository;

use App\Entity\Item;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Item>
 */
class ItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Item::class);
    }



      public function findWithSearch(array $criteria)
    {
        $qb = $this->createQueryBuilder('i')
                   ->orderBy('i.dateLost', 'DESC'); // On trie du plus récent au plus vieux

        // 1. Recherche par Mots Clés (Titre ou Description ou Mots clés)
        // On ne l'ajoute QUE si l'utilisateur a écrit quelque chose
        if (!empty($criteria['keywords'])) {
            $qb->andWhere('i.title LIKE :keywords OR i.description LIKE :keywords OR i.keywords LIKE :keywords')
               ->setParameter('keywords', '%' . $criteria['keywords'] . '%');
        }

        // 2. Recherche par Ville
        // On ne l'ajoute QUE si l'utilisateur a écrit une ville
        if (!empty($criteria['city'])) {
            $qb->andWhere('i.city LIKE :city')
               ->setParameter('city', '%' . $criteria['city'] . '%');
        }

        // 3. Recherche par Catégorie
        // On ne l'ajoute QUE si l'utilisateur a choisi une catégorie
        if (!empty($criteria['category'])) {
            $qb->andWhere('i.category = :category')
               ->setParameter('category', $criteria['category']);
        }

        return $qb->getQuery()->getResult();
    }
}

//    /**
//     * @return Item[] Returns an array of Item objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Item
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

