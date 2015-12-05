<?php

namespace AppBundle\Entity\Repository;

/**
 * PersonRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PersonRepository extends \Doctrine\ORM\EntityRepository
{
	public function findAllQuery()
	{
		return $this->getEntityManager()
            ->createQuery('SELECT c FROM AppBundle:Person c ORDER BY c.lastname, c.firstname ASC');
	}
}
