<?php

namespace App\Repository;

use App\Entity\Campus;
use App\Form\model\ModelCampusVille;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Campus>
 *
 * @method Campus|null find($id, $lockMode = null, $lockVersion = null)
 * @method Campus|null findOneBy(array $criteria, array $orderBy = null)
 * @method Campus[]    findAll()
 * @method Campus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CampusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Campus::class);
    }

    public function save(Campus $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Campus $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findCampusByUser(int $id)
    {

        $query = $this->createQueryBuilder('c');
        $query->leftJoin('user.campus', 'uc')
            ->andWhere('user.id = :idUser')
            ->setParameter('idUser', $id)
            ->getQuery();

        return new $query;
    }
    public function findAllSearch(ModelCampusVille $recherche)
    {

        $qb = $this->createQueryBuilder('c');
        if ($recherche->getRecherche()) {
            $qb->andWhere('c.nom LIKE :recherche')
                ->setParameter('recherche', '%' . $recherche->getRecherche() . '%');
        }


        $query = $qb->getQuery();

        return $query->getResult();

    }

}
