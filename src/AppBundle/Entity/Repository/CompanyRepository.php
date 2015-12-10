<?php

namespace AppBundle\Entity\Repository;

use Pagerfanta\Adapter\DoctrineORMAdapter;
use AppBundle\Entity\Tenant;

/**
 * CompanyRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CompanyRepository extends \Doctrine\ORM\EntityRepository
{
	public function findAllQuery(Tenant $tenant)
	{
		return $this->getEntityManager()
            ->createQuery('SELECT c FROM AppBundle:Company c where c.tenant = :tenant ORDER BY c.name ASC')
            ->setParameter('tenant', $tenant->getId());
	}

	public function findNum(Tenant $tenant)
	{
		return $this->getEntityManager()
            ->createQuery('SELECT count(c) FROM AppBundle:Company c where c.tenant = :tenant')
            ->setParameter('tenant', $tenant->getId())
            ->getSingleResult()[1];
	}
}
