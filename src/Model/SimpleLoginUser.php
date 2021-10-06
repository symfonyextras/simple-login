<?php

namespace Symfonyextars\SimpleLogin\Model;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfonyextars\SimpleLogin\Utility\Hash;

class SimpleLoginUser implements UserInterface
{
    public function __construct($d = [])
    {
        if (is_object($d) && get_class($d) === get_class($this)) {
            $this->username = $d->getUsername();
            $this->roles = $d->getRoles();
            $this->pass = $d->getPassword();
        } else {
            $this->username = $d['login'];
            $this->roles = $d['roles'];
            $this->pass = Hash::encrypt($this->getSalt(), $d['pass']);
        }
    }

    public function isValidPassword(string $rawPass): bool
    {
        return Hash::decrypt($this->getPassword(), $rawPass);
    }

    public function getRoles()
    {
        return $this->roles ?? ['ROLE_USER'];
    }

    public function getPassword()
    {
        return $this->pass;
    }

    public function getSalt()
    {
        return $this->username . '_' . get_class($this);
    }

    public function eraseCredentials()
    {
        $this->pass = '';
    }

    public function getUsername()
    {
        return $this->username;
    }

}