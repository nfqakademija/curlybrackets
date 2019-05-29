<?php


namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Class SavingService
 *
 * @package App\Service
 */
class DoctrineActionsService
{
    /**
     * SavingService constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param $entity
     */
    public function save($entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function remove($entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }
}