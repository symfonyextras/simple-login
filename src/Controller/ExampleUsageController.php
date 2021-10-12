<?php

namespace Symfonyextars\SimpleLogin\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfonyextars\SimpleLogin\Services\LoginCheckerInterface;

class ExampleUsageController extends AbstractController
{
    use ControllerWithLoginCheck;

    public function __construct(LoginCheckerInterface $loginChecker)
    {
        $this->setSimpleLoginChecker($loginChecker);
    }

    public function index(Request $request)
    {
        if (!$this->validSession($request)) {
            return $this->redirect('homepage');
        }
        return $this->render('');
    }
}