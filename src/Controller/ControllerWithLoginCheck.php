<?php

namespace Symfonyextars\SimpleLogin\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfonyextars\SimpleLogin\Model\SimpleLoginUser;
use Symfonyextars\SimpleLogin\Service\LoginCheckerInterface;

trait ControllerWithLoginCheck
{
    /** @var LoginCheckerInterface */
    private $loginChecker;


    /**
     * Call this method inside __construct of the Controller
     * @param LoginCheckerInterface $loginChecker
     */
    public function setSimpleLoginChecker(LoginCheckerInterface $loginChecker): void
    {
        $this->loginChecker = $loginChecker;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function validSession(Request $request): bool
    {
        return $this->loginChecker->isValidSession($request);
    }

    /**
     * @param Request $request
     * @return SimpleLoginUser|null
     */
    public function processLogin(Request $request): ?SimpleLoginUser
    {
        if ($request->isMethod(Request::METHOD_POST)) {
            $login = $request->get(LoginCheckerInterface::LOGIN_NAME, '');
            $pass = $request->get(LoginCheckerInterface::LOGIN_PASS, '');
            if ($user = $this->loginChecker->validateUser($login, $pass)) {
                 if ($this->loginChecker->doLogin($user, $request->getSession())) {
                     return $user;
                 }
            }
        }
        return null;
    }
}