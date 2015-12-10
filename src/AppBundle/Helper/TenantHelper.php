<?php

namespace AppBundle\Helper;

class TenantHelper
{
	protected $session;

	public function __construct($session)
	{
		$this->session = $session;
	}

	public function isTenantObjectOwner($object)
	{
		if (!method_exists($object, 'getTenant'))
		{
			throw new Exception("Error Processing Request", 1);
		}

		return ($object->getTenant()->getId() === $this->session->get('tenant')->getId());
	}
}