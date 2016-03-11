<?php

namespace AppBundle\Entity\Repository;


class InvitationRepository extends \Doctrine\ORM\EntityRepository
{
	public function findAllWithTenant($tenantId)
	{
		$query = $this->getEntityManager()->createQuery('SELECT c FROM AppBundle:Invitation c where c.tenant = :tenant');
        $query->setParameter('tenant', $tenantId);
        return $query;
	}

	public function findById($id)
	{
		$query = $this->getEntityManager()->createQuery('SELECT c FROM AppBundle:Invitation c where c.id = :id');
        $query->setParameter('id', $id);

        return $query;
	}

    public function findAll()
	{
		$query = $this->getEntityManager()->createQuery('SELECT c FROM AppBundle:Invitation c');
        return $query;
    }

	public function findByIdAndTenant($id, $tenantId)
	{
		$query = $this->getEntityManager()->createQuery('SELECT c FROM AppBundle:Invitation c where c.tenant = :tenant and c.id = :id');
        $query->setParameter('tenant', $tenantId);
        $query->setParameter('id', $id);
        return $query;
	}
}
