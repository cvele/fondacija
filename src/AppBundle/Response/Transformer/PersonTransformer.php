<?php

namespace AppBundle\Response\Transformer;

use AppBundle\Entity\Person;
use League\Fractal\TransformerAbstract;

class PersonTransformer extends TransformerAbstract
{
    /**
     * Turn this item object into a generic array
     *
     * @return array
     */
    public function transform(Person $person)
    {
        return [
            'id'              => (int) $person->getId(),
            'firstname'       => $person->getFirstname(),
            'lastname'        => $person->getLastname(),
            'email'           => $person->getEmail(),
            'organizationId'  => $person->getOrganization()->getId(),
            'links' => [
                [
                    'rel' => 'self',
                    'href' => $this->router->generate('app_api_v1_personapi_read', ['id'=>$person->getId()], true)
                ]
            ]
        ];
    }
}
