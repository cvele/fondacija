<?php

namespace AppBundle\Entity\Traits;

trait CreatorAwareTrait
{
	public function getUser() {
        return $this->user;
    }

    public function setUser($user) {
        $this->user = $user;
        return $this;
    }

}
