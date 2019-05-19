<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Product::class);
    }

     /**
      * @return Product[] Returns an array of Product objects
      */

    public function findByActiveProducts()
    {
        return $this->createQueryBuilder('p')
            ->where('p.GivenAway = 0')
            ->andWhere('p.status = 1')
            ->andWhere('p.deadline > CURRENT_TIMESTAMP()')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findProductsByLocation($parameters)
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.location', 'l')
            ->where('l.latitude > :latitudeMin')
            ->andWhere('l.latitude < :latitudeMax')
            ->andWhere('l.longitude > :longitudeMin')
            ->andWhere('l.longitude < :longitudeMax')
            ->setParameters([
                'latitudeMin' => $parameters['latitudeMin'],
                'latitudeMax' => $parameters['latitudeMax'],
                'longitudeMin' => $parameters['longitudeMin'],
                'longitudeMax' => $parameters['longitudeMax'],
            ])
            ->getQuery()
            ->getResult()
            ;
    }


    /*
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
