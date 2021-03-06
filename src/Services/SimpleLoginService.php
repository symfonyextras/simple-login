<?php

namespace Symfonyextars\SimpleLogin\Services;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfonyextars\SimpleLogin\Model\LoginData;
use Symfonyextars\SimpleLogin\Model\SimpleLoginUser;

class SimpleLoginService
{
    const SESSION_LOGIN = 'sfex-login';
    const SESSION_HASH = 'sfex-hash';

    /** @var DataLoader */
    private $dataLoader;
    private $storage;

    public function __construct(DataLoader $dataLoader, Storage $storage)
    {
        $this->dataLoader = $dataLoader;
        $this->storage = $storage;
    }

    /**
     * Search inside storage for params and give answer if user is logged in
     *
     * @param $login
     * @param $hash
     * @return bool
     */
    public function validate($login, $hash): bool
    {
        if ($user = $this->dataLoader->findByLogin($login)) {
            return $this->storage->valid($hash, $user);
        }
        return false;
    }


    public function find($login): ?SimpleLoginUser
    {
        //find user in stack of allowed user to login
        if ($userData = $this->dataLoader->findByLogin($login)) {
            return new SimpleLoginUser($userData);
        }
        return null;
    }

    public function validatePassword(SimpleLoginUser $user, $password): bool
    {
        return $user->isValidPassword($password);
    }


    public function extractLoginHash(Request $request): LoginData
    {
        return new LoginData($request);
    }

    public function doLogin(SimpleLoginUser $user, SessionInterface $session): bool
    {
        if ($hash = $this->storage->store($user)) {
            $session->set(self::SESSION_LOGIN, $user->getUsername());
            $session->set(self::SESSION_HASH, $hash);
            return true;
        }
        return false;
    }

    public function doLogout(Request $request)
    {
        $loginData = $this->extractLoginHash($request);
        $hash = $loginData->getHash();
        if ($hash && $this->storage->has($hash)) {
            // don't care if hash wasn't removed correctly
            $this->storage->remove($hash);
        }
        $this->clearSessionData($request);
    }

    public function clearSessionData(Request $request): void
    {
        $session = $request->getSession();
        $session->remove(self::SESSION_LOGIN);
        $session->remove(self::SESSION_HASH);
    }

}