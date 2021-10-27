<?php

namespace Symfonyextars\SimpleLogin\Model;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfonyextars\SimpleLogin\Utility\Hash;

class SimpleLoginUser implements UserInterface
{
    const GRANTED_ANY = 'any';
    const GRANTED_ALL = 'all';

    public function __construct($d = [])
    {
        if (is_object($d) && get_class($d) === get_class($this)) {
            $this->username = $d->getUsername();
            $this->roles = $d->getRoles();
            $this->pass = $d->getPassword();
        } else {
            $this->username = $d['username'];
            $this->roles = $d['roles'] ?? [];
            $this->pass = $d['pass'] ?? Hash::encrypt(Hash::get(), Hash::get());
        }
    }

    public function isValidPassword(string $rawPass): bool
    {
        return Hash::decrypt($this->getPassword(), $rawPass);
    }

    public function setPassword($p)
    {
        $this->pass = $p;
    }

    public function getRoles()
    {
        return $this->roles ?? ['ROLE_USER'];
    }

    public function setRoles($r)
    {
        $this->roles = $r;
    }

    public function isGranted(string $role): bool
    {
        return in_array($role, $this->getRoles());
    }

    public function areGranted(array $roles = [], $type = self::GRANTED_ALL): bool
    {
        $granted = 0;
        foreach ($roles as $role) {
            if ($this->isGranted($role)) {
                $granted++;
            }
        }

        if ($type === self::GRANTED_ALL) {
            return $granted === count($roles);
        }
        if ($type == self::GRANTED_ANY) {
            return $granted > 0;
        }

        return false;
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

    public function setUsername($u)
    {
        $this->username = $u;
    }

    public function toArray(): array
    {
        return [
            'username' => $this->username,
            'pass' => $this->pass,
            'roles' => $this->roles
        ];
    }

}