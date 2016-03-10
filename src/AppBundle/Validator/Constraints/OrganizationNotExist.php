<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class OrganizationNotExist extends Constraint
{
    public $message = "Organization with id '{{ value }}' does not exist.";

    public function validatedBy()
    {
        return 'organization_not_exist';
    }
}
