<?php

namespace Symfonyextars\SimpleLogin\Services;

use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfonyextars\SimpleLogin\Model\SimpleLoginUser;

class DataLoader
{
    const DEFAULT_USER_LIST_PATH = '/data/user_list.json';

    /** @var KernelInterface */
    private $kernel;
    /** @var ParameterBagInterface */
    private $params;

    private $userListPath;
    /** @var array|[]SimpleLoginUser */
    private $usersList = [];

    public function __construct(KernelInterface $kernel, ParameterBagInterface $params)
    {
        $this->kernel = $kernel;
        $this->params = $params;
        $this->userListPath = $this->params->get('sfex_user_list') ?? self::DEFAULT_USER_LIST_PATH;
        $this->loadData();
    }

    public function findByLogin(string $login): ?SimpleLoginUser
    {
        return $this->usersList[$login] ?? null;
    }

    public function getUsers(): array
    {
        return $this->usersList;
    }

    public function getUsersWithRole($role): array
    {
        return array_filter($this->usersList, static function (SimpleLoginUser $u) use ($role) {
            return $u->isGranted($role);
        });
    }

    /**
     * @throws Exception
     */
    private function loadData(): void
    {
        if ($userListPath = $this->getUserListFilePath()) {
            $dataContent = file_get_contents($userListPath);
            try {
                $data = json_decode($dataContent, true);
                $list = [];
                foreach ($data as $userData) {
                    //login should be present
                    if (isset($userData['login'])) {
                        //password as well with minimum of 4 chars
                        if (isset($userData['pass']) && strlen($userData['pass']) >= 4) {
                            //inform about duplicated entries
                            if (!isset($list[$userData['login']])) {
                                $list[$userData['login']] = new SimpleLoginUser($userData);
                            } else {
                                throw new Exception("duplicated login on user list");
                            }
                        } else {
                            throw new Exception("password is missing or too short");
                        }
                    } else {
                        throw new Exception("missing user login data");
                    }
                }
                $this->usersList = $list;
            } catch (Exception $e) {
                throw new Exception("error reading user list - " . $e->getMessage());
            }
        }// TODO: prepare some king of error message when you are in DEV env to inform you that file is missing
    }

    private function getUserListFilePath(): ?string
    {
        $filePath = $this->kernel->getProjectDir() . DIRECTORY_SEPARATOR . $this->userListPath;
        return file_exists($filePath) ? $filePath : null;
    }

    public function saveData($data = [])
    {
        if ($userListPath = $this->getUserListFilePath()) {
            file_put_contents($userListPath, json_encode($data));
        }
    }
}