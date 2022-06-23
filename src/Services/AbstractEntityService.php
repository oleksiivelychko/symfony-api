<?php

namespace App\Services;

use App\Entity\EntityInterface;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractEntityService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    abstract function list(): array;

    abstract function get(int $id): ?EntityInterface;

    abstract function create(object $dto): ?EntityInterface;

    abstract function update(object $dto, int $id): ?EntityInterface;

    abstract function delete(int $id): ?EntityInterface;

    final public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    final public function persist($entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    final public function remove($entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}