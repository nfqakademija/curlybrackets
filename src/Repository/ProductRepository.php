<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\User;
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
            ->orderBy('p.deadline', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findProductsByLocation($leftTopCorner, $rightBottomCorner)
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.location', 'l')
            ->where('p.GivenAway = 0')
            ->andWhere('p.status = 1')
            ->andWhere('p.deadline > CURRENT_TIMESTAMP()')
            ->andwhere('l.latitude > :latitudeMin')
            ->andWhere('l.latitude < :latitudeMax')
            ->andWhere('l.longitude > :longitudeMin')
            ->andWhere('l.longitude < :longitudeMax')
            ->setParameters([
                'latitudeMin' => $leftTopCorner->getLatitude(),
                'latitudeMax' => $rightBottomCorner->getLatitude(),
                'longitudeMin' => $rightBottomCorner->getLongitude(),
                'longitudeMax' => $leftTopCorner->getLongitude()
            ])
            ->getQuery()
            ->getResult()
            ;
    }
}
