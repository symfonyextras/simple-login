<?php

namespace Symfonyextars\SimpleLogin\Service;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfonyextars\SimpleLogin\Model\SimpleLoginUser;
use Symfonyextars\SimpleLogin\Utility\Hash;

class Storage
{
    const DIR_NAME = 'simple-login';

    /** @var KernelInterface */
    private $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
        $this->init();
    }

    public function has($hash): bool
    {
        return file_exists($this->pathTo($hash));
    }

    public function store(SimpleLoginUser $user): ?string
    {
        $hash = Hash::get(true);
        file_put_contents($this->pathTo($hash), sprintf("%s\t%s", $user->getUsername(), date('Y-m-d H:i:s')));
        return $hash;
    }

    public function remove($hash): bool
    {
        return unlink($this->pathTo($hash));
    }

    public function valid($hash, SimpleLoginUser $user): bool
    {
        if ($this->has($hash)) {
            $cnt = file_get_contents($this->pathTo($hash));
            $parts = explode("\t", $cnt);
            return isset($parts[0]) && $parts[0] == $user->getUsername();
        }
        return false;
    }

    /**
     * Important! -> without leading slash
     * @param string $endpoint
     * @return string
     */
    private function pathTo(string $endpoint): string
    {
        return $this->getStoragePath().DIRECTORY_SEPARATOR.$endpoint;
    }

    private function getStoragePath(): string
    {
        return $this->kernel->getCacheDir().DIRECTORY_SEPARATOR.self::DIR_NAME;
    }

    private function init(): void
    {
        if (!is_dir($this->getStoragePath())) {
            mkdir($this->getStoragePath());
        }
    }
}