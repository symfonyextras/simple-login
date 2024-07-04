Simple Login Controller.
======================

Functionality for small projects allowing to have small set of users who are allowed to access some part of the system.

## Examples

### Controller for login/logut

```php
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfonyextars\SimpleLogin\Services\LoginCheckerInterface;
use Symfonyextars\SimpleLogin\Services\SimpleLoginService;

/**
 * @Route("/auth")
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
```
### Form need to have two fields and submit as `POST`
```html

<form action="/auth/login" method="post">
    <input type="text" name="login" placeholder="yours login here..." autocomplete="off">
    <input type="password" name="pass" placeholder="yours password here..."  autocomplete="off">
    <button>LOGIN</button>
</form>
```

### Inside `FooController.php` which need to verify if user is logged in

```php
<?php 
namespace App\Controller;

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
```
