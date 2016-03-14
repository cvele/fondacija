<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class InvitationValid extends Constraint
{
    public $message = "Invitation code is not valid.";

    public function validatedBy()
    {
        return 'invitation_valid';
    }

}
