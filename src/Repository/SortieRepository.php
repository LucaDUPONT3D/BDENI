<?php

namespace App\Repository;

use App\Entity\Sortie;
use App\Form\model\Model;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function save(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    private function baseQuery(): \Doctrine\ORM\QueryBuilder
    {
        return  $this->createQueryBuilder('s')
            ->leftJoin('s.etat', 'e')
            ->addSelect('e')
            ->leftJoin('s.organisateur', 'o')
            ->addSelect('o')
            ->leftJoin('s.lieu', 'l')
            ->addSelect('l')
            ->leftJoin('s.campus', 'c')
            ->addSelect('c')
            ->leftJoin('s.participants', 'p')
            ->addSelect('p')
            ->orderBy('e.id')
            ->andWhere('e.id != 7');
    }

    public function findAllToCheck()
    {

        $qb = $this->baseQuery();

        $query = $qb->getQuery();
        return $query->getResult();

    }

    public function findAllToDisplay()
    {

        $qb = $this->baseQuery();


        $query = $qb->getQuery();
        return $query->getResult();

    }

    public function findAllToDisplayFilter(Model $model, $user)
    {

        $qb = $this->baseQuery();

        if ($model->getCampus() != null) {
            $qb->andWhere('c.nom = :campus')
                ->setParameter('campus', $model->getCampus());
        }

        if ($model->getRecherche() != null) {
            $qb->andWhere('s.nom LIKE :recherche')
                ->setParameter('recherche', '%' . $model->getRecherche() . '%');
        }

        if ($model->getEntre() != null) {

            $qb->andWhere('s.dateHeureDebut > :datedebut')
                ->setParameter('datedebut', $model->getEntre());
        }

        if ($model->getEt() != null) {

            $qb->andWhere('s.dateHeureDebut < :dateapres')
                ->setParameter('dateapres', $model->getEt());
        }

        if ($model->getOrganisateur() != null) {

            $qb->andWhere('o.id = :organisateur')
                ->setParameter('organisateur', $user);
        }

        if ($model->getPasse() != null) {

            $qb->andWhere('e.id = 5');
        }

        if ($model->getInscrit() != null) {

            $qb->andWhere(':inscrit MEMBER OF s.participants')
                ->setParameter('inscrit', $user);
        }

        if ($model->getPasInscrit() != null) {

            $qb->andWhere(':inscrit NOT MEMBER OF s.participants')
                ->setParameter('inscrit', $user)
                ->andWhere('e.id != 4')
                ->andWhere('e.id != 5')
                ->andWhere('e.id != 6');
        }

        $query = $qb->getQuery();

        return $query->getResult();

    }
}
