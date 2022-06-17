<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Group;
use Doctrine\ORM\EntityManagerInterface;

final class GroupDataPersister implements ContextAwareDataPersisterInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof Group;
    }

    /**
     * Call the persistence layer to save $data
     *
     * @param $data
     * @param array $context
     * @return mixed|object|void
     */
    public function persist($data, array $context = [])
    {
        $this->entityManager->persist($data);
        $this->entityManager->flush();

        return $data;
    }

    /**
     * Call the persistence layer to delete $data
     *
     * @param $data
     * @param array $context
     * @return void
     */
    public function remove($data, array $context = []): void
    {
        $this->entityManager->remove($data);
        $this->entityManager->flush();
    }
}