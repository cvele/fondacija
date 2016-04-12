<?php

namespace AppBundle\Entity\Repository;

trait BaseRepositoryTrait
{
	public function findAllWithTenant($tenantId, $orderByArray = [])
	{
		$order = 'ORDER BY c.createdAt';
		if (count($orderByArray) > 0) {
			$order = 'ORDER BY ';
			foreach($orderByArray as $column => $direction) {
				$order .= 'c.'.$column . ' ' . $direction;
			}
		}

		$query = $this->getEntityManager()
						->createQuery('SELECT c FROM AppBundle:' .$this->entityName. ' c
										WHERE c.tenant = :tenant ' . $order);
        $query->setParameter('tenant', $tenantId);
        return $query;
	}


	public function findById($id)
	{
		$query = $this->getEntityManager()->createQuery('SELECT c FROM AppBundle:' .$this->entityName. ' c where c.id = :id');
        $query->setParameter('id', $id);

        return $query;
	}

    public function findAll($orderByArray = [])
	{
        $order = 'ORDER BY c.createdAt';
        if (count($orderByArray) > 0) {
            $order = 'ORDER BY ';
            foreach($orderByArray as $column => $direction) {
                $order .= 'c.'.$column . ' ' . $direction;
            }
        }
		$query = $this->getEntityManager()->createQuery('SELECT c FROM AppBundle:' .$this->entityName. ' c '.$order);
        return $query;
    }

	public function findByIdAndTenant($id, $tenantId)
	{
		$query = $this->getEntityManager()->createQuery('SELECT c FROM AppBundle:' .$this->entityName. ' c where c.tenant = :tenant and c.id = :id');
        $query->setParameter('tenant', $tenantId);
        $query->setParameter('id', $id);
        return $query;
	}
}
