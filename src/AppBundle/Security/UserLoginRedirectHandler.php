<?php

namespace AppBundle\Security;

use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UserLoginRedirectHandler implements AuthenticationSuccessHandlerInterface
{
    /**
     *
     * @var Doctrine
     */
    private $em;

    private $context;

    private $router;

    public function __construct(EntityManager $em, SecurityContext $context, $router)
    {
        $this->em      = $em;
        $this->context = $context;
        $this->router = $router;
    }

    public function onAuthenticationSuccess(Request $request,  TokenInterface $token)
    {
        $user = $this->context->getToken()->getUser();

        $tenants = $user->getUserTenants();

        if ($tenants->count() > 1)
        {
            $url = $this->router->generate('pick_tenant');
            $response = new RedirectResponse($url);
        }
        else
        {
            $request->getSession()->set('tenant', $tenants->first());
            $url = $this->router->generate('dashboard');
            $response = new RedirectResponse($url);
        }

        return $response;
    }

}