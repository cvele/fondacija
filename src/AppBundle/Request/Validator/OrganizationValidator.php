<?php

namespace AppBundle\Request\Validator;

use Symfony\Component\Validator\Constraints;
use AppBundle\Validator\Constraints\FileNotExist;

class OrganizationValidator extends RequestValidator
{
    public function rules($httpMethod)
    {
        return new Constraints\Collection([
            'allowMissingFields' => (boolean) (strtolower($httpMethod) === 'patch'),
            'fields' => [
                'name' => [
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
                'description' => [
                    new Constraints\Optional(
                        [
                            new Constraints\NotBlank([
                                'message' => "Must not be blank."
                            ]),
                        ]
                    )
                ],
                'logo' => [
                    new Constraints\Optional(
                        [
                            new Constraints\Type([
                                'type' => 'integer',
                                'message' => "The value {{ value }} is not a valid {{ type }}."
                            ]),
                            new FileNotExist([
                                'message' => "File with id '{{ value }}' does not exist."
                            ]),
                        ]
                    )
                ]
            ]
        ]);
    }
}
