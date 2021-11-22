<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ObjectRepository;

class UserRepository
{
    /**
     * @var ObjectRepository
     */
    private ObjectRepository $repo;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;
    /**
     * @var Connection
     */
    private Connection $connection;

    public function __construct(EntityManagerInterface $em, Connection $connection)
    {
        $this->repo = $em->getRepository(User::class);
        $this->em = $em;
        $this->connection = $connection;
    }

    public function find(int $userId): ?User
    {
        /** @var ?User $user */
        $user = $this->repo->find($userId);
        return $user;
    }

    /**
     * @throws EntityNotFoundException
     */
    public function get(int $userId): User
    {
        $user = $this->find($userId);

        if (!$user instanceof User) {
            throw new EntityNotFoundException('User not found');
        }

        return $user;
    }

    /**
     * @throws EntityNotFoundException
     */
    public function getByEmail(string $email): User
    {
        $user = $this->repo->findOneBy(['email' => $email]);

        if (!$user instanceof User) {
            throw new EntityNotFoundException('User not found');
        }

        return $user;
    }

    public function add(User $user): void
    {
        $this->em->persist($user);
    }

    public function remove(User $user): void
    {
        $this->em->remove($user);
    }
}