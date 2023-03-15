<?php

namespace App\Repository;

use App\Entity\Ville;
use App\Form\model\ModelCampusVille;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ville>
 *
 * @method Ville|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ville|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ville[]    findAll()
 * @method Ville[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VilleRepository extends ServiceEntityRepository
{
    const VILLE_LIMIT = 10;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ville::class);
    }

    public function save(Ville $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Ville $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllToDisplay(int $page)
    {
        $offset = ($page - 1) * self::VILLE_LIMIT;

        $qb = $this->createQueryBuilder('v')
            ->setMaxResults(self::VILLE_LIMIT)
            ->setFirstResult($offset);

        $query = $qb->getQuery();

        return $query->getResult();

    }

    public function findAllToCheck()
    {

        $qb = $this->createQueryBuilder('v');

        $query = $qb->getQuery();

        return $query->getResult();

    }


    public function findAllToDisplayFilter(ModelCampusVille $recherche, int $page)
    {
        $offset = ($page - 1) * self::VILLE_LIMIT;

        $qb = $this->createQueryBuilder('v')
            ->setMaxResults(self::VILLE_LIMIT)
            ->setFirstResult($offset);

        if ($recherche->getRecherche()) {
            $qb->andWhere('v.nom LIKE :recherche')
                ->setParameter('recherche', '%' . $recherche->getRecherche() . '%');
        }


        $query = $qb->getQuery();

        return $query->getResult();

    }

    public function findAllToCheckFilter(ModelCampusVille $recherche)
    {

        $qb = $this->createQueryBuilder('v');

        if ($recherche->getRecherche()) {
            $qb->andWhere('v.nom LIKE :recherche')
                ->setParameter('recherche', '%' . $recherche->getRecherche() . '%');
        }


        $query = $qb->getQuery();

        return $query->getResult();

    }
}
