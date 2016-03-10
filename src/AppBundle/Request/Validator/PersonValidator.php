<?php

namespace AppBundle\Request\Validator;

use Symfony\Component\Validator\Constraints;
use AppBundle\Validator\Constraints\OrganizationNotExist;

class PersonValidator extends RequestValidator
{
    public function rules($httpMethod)
    {
        return new Constraints\Collection([
            'allowMissingFields' => (boolean) (strtolower($httpMethod) === 'patch'),
            'fields' => [
                'firstname' => [
                    new Constraints\Required(
                        [
                            new Constraints\NotBlank([
                                'message' => "Field is required and must not be empty."
                            ]),
                            new Constraints\Length([
                                'min' => 3,
                                'max' => 140,
                                'minMessage' => "Must be at least {{ limit }} characters long.",
                                'maxMessage' => "Cannot be longer than {{ limit }} characters.",
                            ]),
                        ]
                    )
                ],
                'lastname' => [
                    new Constraints\Required(
                        [
                            new Constraints\NotBlank([
                                'message' => "Field is required and must not be empty."
                            ]),
                            new Constraints\Length([
                                'min' => 3,
                                'max' => 140,
                                'minMessage' => "Must be at least {{ limit }} characters long.",
                                'maxMessage' => "Cannot be longer than {{ limit }} characters.",
                            ]),
                        ]
                    )
                ],
                'organization' => [
                    new Constraints\Required(
                        [
                            new Constraints\Type([
                                'type' => 'integer',
                                'message' => "The value {{ value }} is not a valid {{ type }}."
                            ]),
                            new OrganizationNotExist([
                                'message' => "Organization with id '{{ value }}' does not exist."
                            ]),
                        ]
                    )
                ]
            ]
        ]);
    }
}
