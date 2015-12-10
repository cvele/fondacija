<?php

namespace AppBundle\Entity\Repository;

use Pagerfanta\Adapter\DoctrineORMAdapter;
use AppBundle\Entity\Tenant;

class DocumentRepository extends \Doctrine\ORM\EntityRepository
{
	public function findAllQuery(Tenant $tenant)
	{
		return $this->getEntityManager()
            ->createQuery('SELECT c FROM AppBundle:Document c where c.tenant = :tenant ORDER BY c.title ASC')
            ->setParameter('tenant', $tenant->getId());
	}

	public function findNum(Tenant $tenant)
	{
		return $this->getEntityManager()
            ->createQuery('SELECT count(c) FROM AppBundle:Document c where c.tenant = :tenant')
            ->setParameter('tenant', $tenant->getId())
            ->getSingleResult()[1];
	}
}
