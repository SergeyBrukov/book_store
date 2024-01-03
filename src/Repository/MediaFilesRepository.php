<?php

namespace App\Repository;

use App\Entity\MediaFiles;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MediaFiles>
 *
 * @method MediaFiles|null find($id, $lockMode = null, $lockVersion = null)
 * @method MediaFiles|null findOneBy(array $criteria, array $orderBy = null)
 * @method MediaFiles[]    findAll()
 * @method MediaFiles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MediaFilesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MediaFiles::class);
    }

//    /**
//     * @return MediaFiles[] Returns an array of MediaFiles objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?MediaFiles
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
