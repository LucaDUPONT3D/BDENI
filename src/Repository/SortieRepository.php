<?php

namespace App\Repository;

use App\Entity\Sortie;
use App\Form\FiltreType;
use App\Form\model\Model;
use Cassandra\Date;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function PHPUnit\Framework\isEmpty;

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
    public function findAlltoCheck()
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

    public function findAlltoDisplay()
    {

        $qb = $this->createQueryBuilder('s');
        $qb->addSelect('s')
            ->leftJoin('s.etat', 'et')
            ->addSelect('et')
            ->leftJoin('s.organisateur', 'us')
            ->addSelect('us')
            ->andWhere('et.id != 7');

        $query = $qb->getQuery();
        return $query->getResult();

    }

    public function findAlltoDisplayFilter(Model $model, $user)
    {

        $qb = $this->createQueryBuilder('s');
        $qb->addSelect('s')
            ->leftJoin('s.etat', 'et')
            ->addSelect('et')
            ->leftJoin('s.organisateur', 'us')
            ->addSelect('us')
            ->leftJoin('s.participants', 'pa')
            ->addSelect('pa')
            ->leftJoin('s.campus', 'ca')
            ->addSelect('ca')
            ->andWhere('et.id != 7');

        if (($model->getCampus()) != null) {
            $qb->andWhere('ca.nom = :campus')
                ->setParameter('campus', $model->getCampus());

        }

        if (($model->getRecherche()) != null) {
            $qb->andWhere('s.nom LIKE :recherche')
                ->setParameter('recherche', '%' . $model->getRecherche() . '%');

        }
        if (($model->getEntre() != null)) {

            $qb->andWhere('s.dateHeureDebut > :datedebut')
                ->setParameter('datedebut', $model->getEntre());
        }
        if (($model->getEt() != null)) {

            $qb->andWhere('s.dateHeureDebut < :dateapres')
                ->setParameter('dateapres', $model->getEt());
        }
        if ($model->getOrganisateur() != null) {

            $qb->andWhere('us.id = :organisateur')
                ->setParameter('organisateur', $user);
        }
        if ($model->getPasse() != null) {

            $qb->andWhere('et.id = 5');

        }

        if ($model->getInscrit() != null) {

            $qb->andWhere(':inscrit MEMBER OF s.participants')
                ->setParameter('inscrit', $user);

        }
        if ($model->getPasInscrit() != null) {

            $qb->andWhere(':inscrit NOT MEMBER OF s.participants')
                ->setParameter('inscrit', $user)
                ->andWhere('et.id != 4')
                ->andWhere('et.id != 5')
                ->andWhere('et.id != 6');


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
