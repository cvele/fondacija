<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class FileNotExist extends Constraint
{
    public $message = "File with id '{{ value }}' does not exist.";

    public function validatedBy()
    {
        return 'file_not_exist';
    }
}
