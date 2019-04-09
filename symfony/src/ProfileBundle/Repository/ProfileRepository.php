<?php

namespace App\ProfileBundle\Repository;

use Pagerfanta\Pagerfanta;

/**
 * ProfileRepository
 */
class ProfileRepository extends AbstractRepository
{
    public const MAX_PER_PAGE = 1000;
    public const MAX_FIND_ELASTICA_RESULT = 10000;

    /**
     * @param int $page
     *
     * @return Pagerfanta
     */
    public function findAllProfiles(int $page = 1): Pagerfanta
    {
        $qb = $this->createQueryBuilder('p');

        return $this->createPaginator($qb->getQuery(), $page, self::NUM_ITEMS);
    }

    /**
     * @param $request
     *
     * @return Pagerfanta
     */
    public function findBySearchQueryInMysql($request): Pagerfanta
    {
        $orderBy = $request->query->get('orderBy', 'firstname');
        $order = $request->query->get('order', 'asc');

        $query = $request->query->get('query', '');
        $queryBy = $request->query->get('queryBy', '');
        $page = $request->query->get('page', 1);
        $limit = $request->query->get('limit', 10) <= self::MAX_PER_PAGE ? $request->query->get(
            'l',
            10
        ) : self::MAX_PER_PAGE;

        $qb = $this
            ->createQueryBuilder('p')
            ->select('p')
            ->orderBy('p.'.$orderBy, $order);

        switch ($queryBy) {
            case 'firstname':
                $qb->where('p.firstname LIKE ?1');
                break;
            case 'lastname':
                $qb->where('p.lastname LIKE ?1');
                break;
            case 'phonenumber':
                $qb->where('p.phonenumber LIKE ?1');
                break;
            default:
                $qb->where('p.firstname LIKE ?1 OR p.lastname LIKE ?1 OR p.phonenumber LIKE ?1');
                break;
        }

        $qb->setParameter(1, '%'.$query.'%');

        return $this->createPaginator($qb->getQuery(), $page, $limit);
    }

}
