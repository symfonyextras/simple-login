<?php

namespace Symfonyextars\SimpleLogin\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfonyextars\SimpleLogin\Model\SimpleLoginUser;

interface LoginCheckerInterface
{
    public const LOGIN_NAME = 'login';
    public const LOGIN_PASS = 'pass';

    public function isValidSession(Request $request): bool;
    public function validateUser($login, $pass): ?SimpleLoginUser;
    public function doLogin(SimpleLoginUser $user, SessionInterface $session): bool;
}