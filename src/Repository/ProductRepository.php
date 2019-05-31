<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * ProductRepository constructor.
     *
     * @param RegistryInterface $registry
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(RegistryInterface $registry, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct($registry, Product::class);
    }

     /**
      * @return Product[] Returns an array of Product objects
      */

    public function findByActiveProducts(): array
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

    /**
     * @param $leftTopCorner
     * @param $rightBottomCorner
     * @return mixed
     */
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
            ->orderBy('p.deadline', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param $entity
     */
    public function save($entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    /**
     * @param $entity
     */
    public function remove($entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }
}
