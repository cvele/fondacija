<?php

namespace AppBundle\Entity\Repository;


class UserRepository extends \Doctrine\ORM\EntityRepository
{
	public function findAllWithTenant($tenantId)
	{
        $dql = "SELECT u, t FROM AppBundle:User u
                LEFT JOIN u.userTenants t
                WHERE t.id = :tenant";

		$query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('tenant', $tenantId);
        return $query;
	}

	public function findById($id)
	{
		$query = $this->getEntityManager()->createQuery('SELECT c FROM AppBundle:User c where c.id = :id');
        $query->setParameter('id', $id);

        return $query;
	}

    public function findAll()
	{
		$query = $this->getEntityManager()->createQuery('SELECT c FROM AppBundle:User c');
        return $query;
    }

	public function findByIdAndTenant($id, $tenantId)
	{
        $dql = "SELECT u, t FROM AppBundle:User u
                LEFT JOIN u.userTenants t
                WHERE t.id = :tenant
                AND u.id = :id";

		$query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('tenant', $tenantId);
        $query->setParameter('id', $id);

        return $query;
	}
}
