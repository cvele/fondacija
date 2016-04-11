<?php

namespace AppBundle\Entity\Repository;


class InvitationRepository extends \Doctrine\ORM\EntityRepository
{
	use BaseRepositoryTrait;
	protected $entityName = 'Invitation';
}
