<?php

namespace AppBundle\Entity\Repository;

use Pagerfanta\Adapter\DoctrineORMAdapter;
/**
 * CompanyRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CompanyRepository extends \Doctrine\ORM\EntityRepository
{
	public function findAllQuery()
	{
		return $this->getEntityManager()
            ->createQuery('SELECT c FROM AppBundle:Company c ORDER BY c.name ASC');
	}

	public function findAllGroupedByDate()
	{
		return $this->getEntityManager()
            ->createQuery('SELECT c FROM AppBundle:Company c GROUP BY c.createdAt')
            ->getResult();
	}
}
