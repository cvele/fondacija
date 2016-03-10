<?php

namespace AppBundle\Entity;

interface CreatorAwareInterface
{
    public function setUser($user);

    public function getUser();
}
