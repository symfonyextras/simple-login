<?php

namespace Symfonyextars\SimpleLogin\Services;

use Symfonyextars\SimpleLogin\Model\SimpleLoginUser;
use Symfonyextars\SimpleLogin\Utility\Hash;

class UserCreator
{
    private $dataLoader;

    public function __construct(DataLoader $dataLoader)
    {
        $this->dataLoader = $dataLoader;
    }

    public function add(string $login, string $password, $roles = [], $attributes = [])
    {
        $user = $this->dataLoader->findByLogin($login);
        if (!$user) {
            $newUser = new SimpleLoginUser(['login' => $login, 'roles' => $roles]);
            $newUser->setPassword(Hash::encrypt($newUser->getSalt(), $password));

            $this->dataLoader->saveData(array_merge(
                $this->dataLoader->getUsers(), $newUser->toArray()
            ));
        }
    }
}