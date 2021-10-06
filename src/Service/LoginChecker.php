<?php

namespace Symfonyextars\SimpleLogin\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfonyextars\SimpleLogin\Model\SimpleLoginUser;

class LoginChecker implements LoginCheckerInterface
{
    private $loginService;

    public function __construct(SimpleLoginService $simpleLoginService)
    {
        $this->loginService = $simpleLoginService;
    }

    public function handleLoginRequest(Request  $request): ?SimpleLoginUser
    {

        return null;
    }

    public function isValidSession(Request $request): bool
    {
        $session = $request->getSession();
        $login = $session->get(SimpleLoginService::SESSION_LOGIN, null);
        $hash = $session->get(SimpleLoginService::SESSION_HASH, null);

        return $this->loginService->validate($login, $hash);
    }

    /**
     * Check if user exist and provided correct password, on:
     * - true ->  UserInterface object with setup parameters (roles, attributes, etc)
     * - false -> null
     *
     * @param $login
     * @param $pass
     * @return SimpleLoginUser|null
     */
    public function validateUser($login, $pass): ?SimpleLoginUser
    {
        // TODO: shall be done
        // todo: find using loginService user with given login and validate pass

        if ($user = $this->loginService->find($login)) {
             if ($this->loginService->validatePassword($user, $pass)) {
                 return $user;
             }
        }

        return null;
    }

    /**
     * For a given UserInterface object store data inside session storage
     *
     * @param SimpleLoginUser $user
     * @param SessionInterface $session
     * @return bool
     */
    public function doLogin(SimpleLoginUser $user, SessionInterface $session): bool
    {
        return $this->loginService->doLogin($user, $session);
    }

    /**
     * @param Request $request
     */
    public function doLogout(Request $request): void
    {
        // TODO: shall be done
        // TODO: remove from store hash assigned for this request
    }
}