<?php

namespace AppBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class EntityEvent extends Event
{
	/**
	 * @var object
	 */
	private $entity;

	public function __construct($entity)
	{
		$this->entity = $entity;
	}

	public function getEntity()
	{
		return $this->entity;
	}
}
