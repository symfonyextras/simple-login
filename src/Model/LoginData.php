<?php

namespace Symfonyextars\SimpleLogin\Model;

use Symfony\Component\HttpFoundation\Request;
use Symfonyextars\SimpleLogin\Services\SimpleLoginService;

class LoginData
{
    private $login;
    private $hash;

    public function __construct(?Request $request)
    {
        if ($request !== null) {
            $this->extract($request);
        }
    }

    public function extract(Request $request): self
    {
        $session = $request->getSession();
        $this->login = $session->get(SimpleLoginService::SESSION_LOGIN, null);
        $this->hash = $session->get(SimpleLoginService::SESSION_HASH, null);

        return $this;
    }

    public function hasParams(): bool
    {
        return $this->login && $this->hash;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }
}