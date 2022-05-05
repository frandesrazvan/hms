<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Hotel;
use App\Entity\User;
use App\Enum\StaffEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    private function createPaginationQuery(int $pageNumber)
    {
        return $this->createQueryBuilder('u')
            ->setFirstResult(($pageNumber - 1) * StaffEnum::NUMBER_USERS_ON_PAGE)
            ->setMaxResults(StaffEnum::NUMBER_USERS_ON_PAGE);

    }

    public function findStaff(string|Hotel $hotel, int $pageNumber): array
    {
        return $this->createPaginationQuery($pageNumber)
            ->select('u.id', 'u.firstName', 'u.lastName', 'u.roles', 'u.email', 'u.dateOfBirth', 'u.registrationDate')
            ->where('u.hotel = :val')
            ->andWhere('u.deletedAt IS NULL')
            ->setParameter('val', $hotel)
            ->getQuery()
            ->getResult();
    }

    public function findNumberStaff(string|Hotel $hotel): array
    {
        return $this->createQueryBuilder('u')
            ->select('count(u.id)')
            ->where('u.hotel = :val')
            ->andWhere('u.deletedAt IS NULL')
            ->setParameter('val', $hotel)
            ->getQuery()
            ->getResult();
    }

    public function findOwnersStaff(User $user, int $pageNumber): array
    {
        return $this->createPaginationQuery($pageNumber)
            ->select('u.id', 'u.firstName', 'u.lastName', 'u.roles', 'u.email', 'u.dateOfBirth', 'u.registrationDate')
            ->join('App\Entity\Hotel', 'h', \Doctrine\ORM\Query\Expr\Join::WITH, 'u.hotel = h.id')
            ->where('h.owner = :val')
            ->andWhere('u.deletedAt IS NULL')
            ->setParameter('val', $user)
            ->getQuery()
            ->getResult();
    }

    public function findOwnersNumberStaff(User $user): array
    {
        return $this->createQueryBuilder('u')
            ->select('count(u.id)')
            ->join('App\Entity\Hotel', 'h', \Doctrine\ORM\Query\Expr\Join::WITH, 'u.hotel = h.id')
            ->where('h.owner = :val')
            ->andWhere('u.deletedAt IS NULL')
            ->setParameter('val', $user)
            ->getQuery()
            ->getResult();
    }
}
