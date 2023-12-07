<?php

namespace App\Repository;

use App\Entity\DeliveryPaymentMethods;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DeliveryPaymentMethods>
 *
 * @method DeliveryPaymentMethods|null find($id, $lockMode = null, $lockVersion = null)
 * @method DeliveryPaymentMethods|null findOneBy(array $criteria, array $orderBy = null)
 * @method DeliveryPaymentMethods[]    findAll()
 * @method DeliveryPaymentMethods[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeliveryPaymentMethodsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeliveryPaymentMethods::class);
    }

//    /**
//     * @return DeliveryPaymentMethods[] Returns an array of DeliveryPaymentMethods objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DeliveryPaymentMethods
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
