<?php

namespace Symfonyextars\SimpleLogin\Services;

use Symfonyextars\SimpleLogin\Model\SimpleLoginUser;

class UserUpdater
{
    private $dataLoader;

    public function __construct(DataLoader $dataLoader)
    {
        $this->dataLoader = $dataLoader;
    }

    public function changeRoles($username, $roles = [])
    {
        $this->withSimpleUserLogin($username, static function (SimpleLoginUser $user) use ($roles) {
            $user->setRoles($roles);
        });
    }

    public function changePassword($username, $newPassword)
    {
        $this->withSimpleUserLogin($username, static function (SimpleLoginUser $user) use ($newPassword) {
            $user->setPassword(UserCreator::passwordEncrypt($user, $newPassword));
        });
    }

    public function withSimpleUserLogin($username, callable $updateFn)
    {
        $user = $this->dataLoader->findByLogin($username);
        if ($user) {
            if (is_callable($updateFn)) {
                $updateFn($user);
            }
            $this->dataLoader->updateData($user, true);
        }
    }
}