<?php

namespace Symfonyextars\SimpleLogin\Services;

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
    public function extractSimpleLoginUser(Request $request): ?SimpleLoginUser;
    public function handleSimpleLoginRequest(Request $request): ?SimpleLoginUser;
}