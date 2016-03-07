<?php

namespace AppBundle\Entity\Repository;

class DocumentRepository extends \Doctrine\ORM\EntityRepository
{
	public function findAllQuery($tenantId)
	{
		return $this->getEntityManager()
            ->createQuery('SELECT c FROM AppBundle:Document c where c.tenant = :tenant ORDER BY c.title ASC')
            ->setParameter('tenant', $tenantId);
	}

	public function findNum($tenantId)
	{
		return $this->getEntityManager()
            ->createQuery('SELECT count(c) FROM AppBundle:Document c where c.tenant = :tenant')
            ->setParameter('tenant', $tenantId)
            ->getSingleResult()[1];
	}
}
