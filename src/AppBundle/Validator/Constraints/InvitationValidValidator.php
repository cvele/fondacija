<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class InvitationValidValidator extends ConstraintValidator
{
    protected $manager;

    function __construct($manager)
    {
        $this->manager = $manager;
    }

    public function validate($value, Constraint $constraint)
    {
        $email = isset($this->context->getRoot()['email'])?$this->context->getRoot()['email']:null;
        $invitation = $this->manager->
                            getRepo()
                            ->findOneBy([
                                'code' => $value,
                                'email' => $email
                            ]);
        if ($invitation === null) {
            $this->context->buildViolation($constraint->message,[
                '{{ value }}' => $value
            ])
            ->addViolation()
            ;
        }
    }
}
