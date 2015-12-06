<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use AppBundle\Entity\Person;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/app")
 */
class ContactController extends Controller
{
    /**
     * @Route("/contact", name="contact_list")
     * @Template("AppBundle:Contact:list.html.twig")
     */
    public function listContactsAction(Request $request)
    {
        $q = $this->getDoctrine()
                    ->getRepository('AppBundle:Person')
                    ->findAllQuery();

        $adapter = new DoctrineORMAdapter($q);

        $contacts = new Pagerfanta($adapter);
        $contacts->setMaxPerPage(20);

        return ['contacts' => $contacts];
    }


    /**
     * @Route("/contacts_delete", name="contacts_delete_multiple", options={"expose"=true})
     */
    public function deleteContactsAction(Request $request)
    {
        $ids  = $request->request->get('ids');
        $em   = $this->getDoctrine()->getEntityManager();
        $repo = $this->getDoctrine()->getRepository('AppBundle:Person');

        if (!is_array($ids))
        {
            return new JsonResponse(['deleted' => []]);
        }

        $deleted = [];
        foreach ($ids as $key => $id) {
            $contact = $repo->findOneBy(['id'=>$id]);

            if ($contact == null)
            {
                continue;
            }

            $em->remove($contact);
            $em->flush();

            $deleted[] = $id;
        }

        return new JsonResponse(['deleted' => $deleted]);
    }

    /**
     * @Route("/contact/new", name="contact_new")
     * @Template("AppBundle:Contact:create.html.twig")
     */
    public function createContactAction(Request $request)
    {
        $person = new Person();

        $form = $this->get('app.form.person')->setData($person);
        $form->handleRequest($request);

        $user = $this->get('security.context')->getToken()->getUser();

        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $person->setUser($user);
            $em->persist($person);
            $em->flush();

            return $this->redirect($this->generateUrl(
                'contact_show',
                ['id' => $person->getId()]
            ));
        }

        return ['form'=>$form->createView()];
    }

    /**
     * @Route("/contact/{id}/edit", name="contact_edit")
     * @Template("AppBundle:Contact:edit.html.twig")
     * @ParamConverter("contact", class="AppBundle:Person")
     */
    public function editCompanyAction(Person $contact, Request $request)
    {
        $form = $this->get('app.form.person')->setData($contact);
        $form->handleRequest($request);

        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush();

            return $this->redirect($this->generateUrl(
                'contact_show',
                array('id' => $contact->getId())
            ));
        }

        return ['form' => $form->createView(), 'contact' => $contact];
    }

    /**
     * @Route("/contact/{id}/show", name="contact_show")
     * @Template("AppBundle:Contact:show.html.twig")
     * @ParamConverter("contact", class="AppBundle:Person")
     */
    public function showContactAction(Person $contact, Request $request)
    {

        return ['contact' => $contact];
    }
}