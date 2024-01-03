<?php

namespace App\Services;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class CustomFiltersForCollection
{
    public function __construct()
    {
    }

    /**
     * @param array $filtersQuery
     * @param ServiceEntityRepository $repository
     * @param string $repositoryName
     * @return array
     */
    public function customFilters(array $filtersQuery, ServiceEntityRepository $repository, string $repositoryName): array
    {
        $qb = $repository->createQueryBuilder($repositoryName);

        foreach ($filtersQuery as $item => $value) {
            $this->applyFilter($qb, $repositoryName, $item, $value);
        }

        return $qb->getQuery()->getResult();
    }

    private function applyFilter($qb, $repositoryName, $item, $value): void
    {
        if ($item === 'name') {
            $qb
                ->andWhere($qb->expr()->like("$repositoryName.$item", ':val'))
                ->setParameter('val', '%' . $value . '%');
        } else {
            $qb
                ->andWhere("$repositoryName.$item = :val")
                ->setParameter('val', $value);
        }
    }
}