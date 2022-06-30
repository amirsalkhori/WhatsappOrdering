<?php

namespace App\Repository;

use App\Entity\Discount;
use App\Entity\Enum\DiscountTypeEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Discount>
 *
 * @method Discount|null find($id, $lockMode = null, $lockVersion = null)
 * @method Discount|null findOneBy(array $criteria, array $orderBy = null)
 * @method Discount[]    findAll()
 * @method Discount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DiscountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Discount::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Discount $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Discount $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Discount[] Returns an array of Discount objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Discount
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function checkValidDiscount($code): ?Discount
    {
        $currentDateTime = new \DateTime();
        $today = $currentDateTime->format('Y-m-d H:i:s');

        return $this->createQueryBuilder('d')
            ->where('d.status = true AND d.discountType = :discountType AND (d.endTime >= :today AND  d.startTime <= :today) AND d.code = :code')
            ->setParameter('today', $today)
            ->setParameter('code', $code)
            ->setParameter('discountType', DiscountTypeEnum::CHARGE_CODE)
            ->orderBy('d.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findValidDiscount($code): ?Discount
    {
        $currentDateTime = new \DateTime();
        $today = $currentDateTime->format('Y-m-d H:i:s');

        return $this->createQueryBuilder('d')
            ->where('d.status = true AND d.discountType = :discountType AND (d.endTime >= :today AND  d.startTime <= :today) AND d.code = :code')
            ->setParameter('today', $today)
            ->setParameter('code', $code)
            ->setParameter('discountType', DiscountTypeEnum::DISCOUNT_CODE)
            ->orderBy('d.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
