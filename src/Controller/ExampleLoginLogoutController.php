<?php

namespace Symfonyextars\SimpleLogin\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfonyextars\SimpleLogin\Service\LoginCheckerInterface;
use Symfonyextars\SimpleLogin\Service\SimpleLoginService;

/**
 * @Route("/security")
 */
class ExampleLoginLogoutController extends AbstractController
{
    // use this trail to process login/logout methods
    use ControllerWithLoginCheck;

    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/login", name="sfex_login")
     */
    public function login(Request $request): Response
    {
        if ($request->isMethod(Request::METHOD_POST)) {
            // process validation
            $user = $this->processLogin($request);
            if ($user) {
                // ... redirect user when correctly verified to secret page
            } else {
                // ... user not logged in - add error Flash informing incorrect login
            }
        }
        //show form
        return $this->render('login/form.html.twig');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     *
     * @Route("/logout", name="sfex_logout")
     */
    public function logout(Request $request): RedirectResponse
    {
        //inside session is everything needed to logout user
        $this->logout($request);
        //then redirect user after
        return new RedirectResponse($this->generateUrl('default'));
    }
}