<?php

namespace App\Repository;

use App\Entity\User;
use App\Form\model\ModelCampusVille;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface,UserLoaderInterface
{
    const USER_LIMIT = 10;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
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

        $this->save($user, true);
    }

    public function loadUserByIdentifier(string $username)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.pseudo = :username')
            ->orWhere('u.email = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->getOneOrNullResult();

    }

    public function loadUserByUsername(string $username)
    {
        return $this->loadUserByIdentifier($username);
    }

    public function findOneToDisplay(int $id)
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.campus', 'c')
            ->addSelect('c')
            ->leftJoin('u.sorties', 's')
            ->addSelect('s')
            ->leftJoin('u.inscriptions', 'i')
            ->addSelect('i')
            ->andWhere('u.id = :userId')
            ->setParameter('userId', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
    public function findAllToDisplay(int $page)
    {
        $offset = ($page - 1) * self::USER_LIMIT;

        $qb = $this->createQueryBuilder('u')
            ->setMaxResults(self::USER_LIMIT)
            ->setFirstResult($offset);

        $query = $qb->getQuery();
        return $query->getResult();

    }

    public function findAllToCheck()
    {

        $qb = $this->createQueryBuilder('u');

        $query = $qb->getQuery();
        return $query->getResult();

    }

    public function findAllToDisplayFilter(ModelCampusVille $recherche, int $page)
    {
        $offset = ($page - 1) * self::USER_LIMIT;

        $qb = $this->createQueryBuilder('u')
            ->setMaxResults(self::USER_LIMIT)
            ->setFirstResult($offset);


        if ($recherche->getRecherche()) {
            $qb->andWhere('u.pseudo LIKE :recherche')
                ->setParameter('recherche', '%' . $recherche->getRecherche() . '%');
        }

        $query = $qb->getQuery();
        return $query->getResult();

    }

    public function findAllToCheckFilter(ModelCampusVille $recherche)
    {

        $qb = $this->createQueryBuilder('u');

        if ($recherche->getRecherche()) {
            $qb->andWhere('u.pseudo LIKE :recherche')
                ->setParameter('recherche', '%' . $recherche->getRecherche() . '%');
        }

        $query = $qb->getQuery();
        return $query->getResult();

    }



}
