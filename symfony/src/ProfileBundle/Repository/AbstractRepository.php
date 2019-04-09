<?php

namespace App\ProfileBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Doctrine\ORM\Query;

abstract class AbstractRepository extends EntityRepository
{
    public const NUM_ITEMS = 10;

    protected function createPaginator(Query $query, int $page, int $limit): Pagerfanta
    {
        $paginator = new Pagerfanta(new DoctrineORMAdapter($query));
        $paginator->setMaxPerPage(self::NUM_ITEMS);
        $paginator->setCurrentPage($page);
        $paginator->setMaxPerPage($limit);

        return $paginator;
    }
}