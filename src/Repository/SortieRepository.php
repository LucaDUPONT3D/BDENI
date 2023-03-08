<?php

namespace App\Repository;

use App\Entity\Sortie;
use App\Form\FiltreType;
use Cassandra\Date;
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

//    /**
//     * @return Sortie[] Returns an array of Sortie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Sortie
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function findALLjoin()
    {

        $qb = $this->createQueryBuilder('s');
        $qb->addSelect('s')
            ->leftJoin('s.etat', 'et')
            ->addSelect('et')
            ->leftJoin('s.organisateur', 'us')
            ->addSelect('us');

        $query = $qb->getQuery();
        return $query->getResult();

    }

    public function findALLFilter($campus, $recherche, $entre, $et, $organisateur, $passe)
    {

        $qb = $this->createQueryBuilder('s');
        $qb = $this->createQueryBuilder('s');
        $qb->addSelect('s')
            ->leftJoin('s.etat', 'et')
            ->addSelect('et')
            ->leftJoin('s.organisateur', 'us')
            ->addSelect('us')
            ->leftJoin('s.campus', 'ca')
            ->addSelect('ca')
            ->andWhere('ca.nom = :campus')
            ->setParameter('campus', $campus);
        if (isset($recherche)) {
            $qb->andWhere('s.nom LIKE :recherche')
                ->setParameter('recherche', '%' . $recherche . '%');

        }
        if (isset($entre)) {

            $qb->andWhere('s.dateHeureDebut > :datedebut')
                ->setParameter('datedebut', $entre);
        }
        if (isset($et)) {

            $qb->andWhere('s.dateHeureDebut < :dateapres')
                ->setParameter('dateapres', $et);
        }
        if (isset($organisateur)) {

            $qb->andWhere('us.email = :organisateur')
                ->setParameter('organisateur', $organisateur);
        }
        if (isset($passe)) {

            $qb->andWhere('s.dateLimiteInscription > :mtn')
                ->setParameter('mtn', $passe);

        }


        $query = $qb->getQuery();

        return $query->getResult();

    }

    public function findjoin($id)
    {

        $qb = $this->createQueryBuilder('s');
        $qb->addSelect('s')
            ->leftJoin('s.etat', 'et')
            ->addSelect('et')
            ->leftJoin('s.organisateur', 'us')
            ->addSelect('us')
            ->andWhere('s.id= :id')
            ->setParameter('id', $id);

        $query = $qb->getQuery();
        return $query->getResult();

    }
}
