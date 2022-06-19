<?php

namespace App\Services;

use App\Dto\Http\RequestDTOInterface;
use App\Entity\EntityInterface;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractEntityService
{
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    abstract function list(): array;

    abstract function get(int $id): ?EntityInterface;

    abstract function create(object $dto): ?EntityInterface;

    abstract function update(object $dto, int $id): ?EntityInterface;

    abstract function delete(int $id): ?EntityInterface;

    final function persistAndFlush($entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    final function removeAndFlush($entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }
}