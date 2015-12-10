<?php

namespace AppBundle\Event\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Tenant;

class TenantListener implements EventSubscriberInterface
{
	private $defaultTenant = null;
    private $em;
	private $router;
	private $context;

	public function __construct(EntityManager $em, Router $router, SecurityContext $context)
	{
		$this->router        = $router;
		$this->context       = $context;
		$this->em            = $em;
	}

	public function onKernelRequest(GetResponseEvent $event)
	{
		$request = $event->getRequest();
		$hasNewTenant = false;

		if($request->query->has('tenant_id'))
		{
			$tenant = $this->em
							->getRepository('AppBundle:Tenant')
							->find($request->query->get('tenant_id'));

			if ($tenant != null)
			{
				$this->defaultTenant = $tenant;
				$hasNewTenant = true;
			}
		}

		if (($this->context->isGranted('ROLE_USER') or $this->context->isGranted('ROLE_ADMIN')) and $hasNewTenant == false)
		{
			$this->defaultTenant = $request->getSession()->get('tenant');
		}

		$request->getSession()->set('tenant', $this->defaultTenant);

		if (is_object($request->getSession()->get('tenant')) and $request->getSession()->get('tenant') instanceof Tenant)
		{
			$tenant = $this->em
							->getRepository('AppBundle:Tenant')
							->find($request->getSession()->get('tenant')->getId());

			if (!$this->context->getToken()->getUser()->getUserTenants()->contains($tenant))
			{
				$url      = $this->router->generate('fos_user_security_logout');
				$response = new RedirectResponse($url);
	            $event->setResponse($response);
	            return $response;
			}
		}
	}

	public static function getSubscribedEvents()
	{
		return array(
			// must be registered before the default Locale listener
			KernelEvents::REQUEST => [['onKernelRequest', 17]],
		);
	}
}