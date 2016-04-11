<?php

namespace AppBundle\Request\Validator;

use Symfony\Component\Validator\Constraints;
use AppBundle\Validator\Constraints\UniqueEmail;
use AppBundle\Validator\Constraints\InvitationValid;

class UserValidator extends RequestValidator
{
    public function rules($httpMethod)
    {
        return new Constraints\Collection([
            'allowMissingFields' => (boolean) (strtolower($httpMethod) === 'patch'),
            'fields' => [
                'id' => [
                    new Constraints\Optional(
                        [
                            new Constraints\Type([
                                'type' => 'integer'
                            ]),
                        ]
                    )
                ],
                'code' => [
                    new Constraints\Optional(
                        [
                            new Constraints\NotBlank([
                                'message' => "Invitation is required and must not be empty."
                            ]),
                            new InvitationValid(),
                        ]
                    )
                ],
                'avatar' => [
                    new Constraints\Optional(
                        [
                            new Constraints\Image([
                                'maxSize'        => "1024k",
                                'maxSizeMessage' => 'The avatar is too large ({{ size }} {{ suffix }}). Allowed maximum size is {{ limit }} {{ suffix }}.',
                            ])
                        ]
                    )
                ],
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
                'email' => [
                    new Constraints\Required(
                        [
                            new Constraints\NotBlank([
                                'message' => "Field is required and must not be empty."
                            ]),
                            new Constraints\Email([
                                'message' => "Email address is not valid."
                            ]),
                            new UniqueEmail($httpMethod),
                        ]
                    )
                ],
                'password' => [
                    new Constraints\Required(
                        [
                            new Constraints\NotBlank([
                                'message' => "Password is required and must not be empty."
                            ]),
                            new Constraints\Length([
                                'min' => 5,
                                'minMessage' => "Password must be at least {{ limit }} characters long."
                            ]),
                        ]
                    )
                ]
            ]
        ]);
    }
}
