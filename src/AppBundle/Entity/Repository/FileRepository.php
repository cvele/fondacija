<?php

namespace AppBundle\Entity\Repository;


class FileRepository extends \Doctrine\ORM\EntityRepository
{
	use BaseRepositoryTrait;
	protected $entityName = 'File';
}
