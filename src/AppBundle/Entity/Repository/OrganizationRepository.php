<?php

namespace AppBundle\Entity\Repository;


class OrganizationRepository extends \Doctrine\ORM\EntityRepository
{
	use BaseRepositoryTrait;
	protected $entityName = 'Organization';
}
