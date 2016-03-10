<?php

namespace AppBundle\Response\Transformer;

use AppBundle\Entity\Invitation;
use League\Fractal\TransformerAbstract;

class InvitationTransformer extends TransformerAbstract
{
    /**
     * Turn this item object into a generic array
     *
     * @return array
     */
    public function transform(Invitation $invitation)
    {
        return [
            'id'        => (int) $invitation->getId(),
            'code'      => $invitation->getCode(),
            'isSent'    => $invitation->isSent(),
            'email'     => $invitation->getEmail()
        ];
    }
}
