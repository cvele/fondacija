<?php

namespace AppBundle\Entity\Repository;


class PersonRepository extends \Doctrine\ORM\EntityRepository
{
	use BaseRepositoryTrait;
	protected $entityName = 'Person';
}
