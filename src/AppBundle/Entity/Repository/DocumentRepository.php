<?php

namespace AppBundle\Entity\Repository;

use Pagerfanta\Adapter\DoctrineORMAdapter;

class DocumentRepository extends \Doctrine\ORM\EntityRepository
{
	public function findAllQuery()
	{
		return $this->getEntityManager()
            ->createQuery('SELECT c FROM AppBundle:Document c ORDER BY c.title ASC');
	}

	public function findNum()
	{
		return $this->getEntityManager()
            ->createQuery('SELECT count(c) FROM AppBundle:Document c')
            ->getSingleResult()[1];
	}
}
