<?php

namespace AppBundle\Entity\Repository;


class UserRepository extends \Doctrine\ORM\EntityRepository
{

	use BaseRepositoryTrait {
		findAllWithTenant as traitFindAllWithTenant;
		findByIdAndTenant as traitFindByIdAndTenant;
	}

	protected $entityName = 'User';

	public function findAllWithTenant($tenantId, $orderBy = null)
	{
		$order = 'ORDER BY u.createdAt';
		if (count($orderByArray) > 0) {
			$order = 'ORDER BY ';
			foreach($orderByArray as $column => $direction) {
				$order .= 'u.'.$column . ' ' . $direction;
			}
		}

        $dql = "SELECT u, t FROM AppBundle:User u
                LEFT JOIN u.userTenants t
                WHERE t.id = :tenant " . $order;

		$query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('tenant', $tenantId);

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
