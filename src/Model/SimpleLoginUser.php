<?php

namespace Symfonyextars\SimpleLogin\Model;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfonyextars\SimpleLogin\Utility\Hash;

class SimpleLoginUser implements UserInterface
{
    public function __construct($d = [])
    {
        $this->username = $d['login'];
        $this->roles = $d['roles'];
        $this->pass = Hash::encrypt($this->getSalt(), $d['pass']);
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