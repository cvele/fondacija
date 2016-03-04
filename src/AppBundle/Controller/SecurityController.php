<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @Route("/")
 */
class SecurityController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        return [];
    }

    /**
     * @Route("/{username}/salt", requirements={"username" = "\w+"})
     */
    public function saltAction($username)
    {
        $userManager = $this->container->get('fos_user.user_manager');

        $user = $userManager->findUserByUsernameOrEmail($username);
        if ($user === null) {
            throw new HttpException(400, "Error User Not Found");
        }

        return new JsonResponse(['salt' => $user->getSalt()]);
    }

    /**
     * @Route("/api/{username}/info", requirements={"username" = "\w+"})
     */
    public function infoAction($username)
    {
        $userManager = $this->container->get('fos_user.user_manager');

        $user = $userManager->findUserByUsernameOrEmail($username);
        if ($user === null) {
            throw new HttpException(400, "Error User Not Found");
        }

        return new JsonResponse($user->toArray());
    }
}
