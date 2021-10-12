<?php

namespace Symfonyextars\SimpleLogin\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfonyextars\SimpleLogin\Model\SimpleLoginUser;
use Symfonyextars\SimpleLogin\Services\LoginCheckerInterface;

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
        return $this->loginChecker->handleSimpleLoginRequest($request);
    }

    public function getSimpleLoginUser(Request $request): ?SimpleLoginUser
    {
        return $this->loginChecker->extractSimpleLoginUser($request);
    }
}