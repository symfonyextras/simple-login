<?php

namespace Symfonyextars\SimpleLogin\Services;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfonyextars\SimpleLogin\Model\LoginData;
use Symfonyextars\SimpleLogin\Model\SimpleLoginUser;

class LoginChecker implements LoginCheckerInterface
{
    /** @var SimpleLoginService */
    private $loginService;
    /** @var DataLoader */
    private $dataLoader;

    public function __construct(SimpleLoginService $simpleLoginService, DataLoader $dataLoader)
    {
        $this->loginService = $simpleLoginService;
        $this->dataLoader = $dataLoader;
    }

    /**
     * Method used inside AbstractController to get user object
     *
     * @param Request $request
     * @return SimpleLoginUser|null
     */
    public function extractSimpleLoginUser(Request $request): ?SimpleLoginUser
    {
        $loginData = $this->extractLoginHash($request);
        if ($loginData->hasParams()) {
            return $this->dataLoader->findByLogin($loginData->getLogin()) ?? null;
        }
        return null;
    }

    /**
     * Process login used right from Request, executed inside `/login route` method
     *
     * @param Request $request
     * @return SimpleLoginUser|null
     */
    public function handleSimpleLoginRequest(Request $request): ?SimpleLoginUser
    {
        if ($request->isMethod(Request::METHOD_POST)) {
            $login = $request->get(LoginCheckerInterface::LOGIN_NAME, null);
            $pass = $request->get(LoginCheckerInterface::LOGIN_PASS, null);
            if (($login && $pass) &&
                $simpleLoginUser = $this->validateUser($login, $pass)) {
                $this->doLogin($simpleLoginUser, $request->getSession());
                return $simpleLoginUser;
            }
        }
        return null;
    }


    private function extractLoginHash(Request $request): LoginData
    {
        return $this->loginService->extractLoginHash($request);
    }

    public function isValidSession(Request $request): bool
    {
        $loginData = $this->extractLoginHash($request);

        return $loginData->hasParams() &&
            $this->loginService->validate($loginData->getLogin(), $loginData->getHash());
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
        if ($user = $this->loginService->find($login)) {
            if (
                $user->canLogin() &&
                $this->loginService->validatePassword($user, $pass)
            ) {
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
        $sessionData = $this->extractLoginHash($request);
        if ($sessionData->hasParams()) {
            $this->loginService->doLogout($request);
        }
    }
}