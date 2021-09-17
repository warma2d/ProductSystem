<?php

namespace ProductSystem\Secondary\Controllers;

use Bramus\Router\Router;
use Jasny\Auth\Auth;
use Jasny\Auth\LoginException;
use ProductSystem\Core\Repository\ProductRepository;
use ProductSystem\Core\Service\LoginValidator;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Waavi\Sanitizer\Sanitizer;

class GuestController extends AbstractController {

    private Environment $twig;

    private const FILTERS = [
            'email' => 'trim|escape|lowercase',
            'password' => 'trim|escape',
        ];

    private Router $router;

    private Auth $auth;

    public function __construct(Router $router, Auth $auth)
    {
        parent::__construct();

        $this->router = $router;
        $this->auth = $auth;

        $loader = new FilesystemLoader('./Secondary/Pages');
        $this->twig = new Environment($loader);

        $this->router->get('/', [$this, 'homePage']);
        $this->router->get('/auth', [$this, 'authForm']);
        $this->router->post('/auth', [$this, 'authHandler']);
        $this->router->get('/products', [$this, 'productList']);
    }

    public function homePage()
    {
        header('Location: /auth');
    }

    public function authForm(): void
    {
        echo $this->twig->render('guest/login.html.twig', [
            'errors' => $this->sessionStorage->getErrors(),
            'post' => $this->sessionStorage->getPostData()
        ]);
    }

    public function authHandler()
    {
        $sanitizer  = new Sanitizer($_POST, self::FILTERS);
        $inputData = $sanitizer->sanitize();
        $errors = LoginValidator::validate($inputData);

        if ($errors) {
            $this->sessionStorage->saveErrors($errors);
            $this->sessionStorage->savePostData($_POST);
            header('Location: /auth');
        } else {
            try {
                $this->auth->login($inputData['email'], $inputData['password']);
            } catch (LoginException $exception) {
                $this->sessionStorage->saveErrors($exception->getMessage());
                header('Location: /auth');
            }

            header('Location: /');
        }
    }

    public function productList()
    {
        $products = (new ProductRepository())->getAllInterfaceProducts();
        echo $this->twig->render('guest/product_list.html.twig', [
            'message' => $this->sessionStorage->getMessage(),
            'errors' => $this->sessionStorage->getErrors(),
            'products' => $products
        ]);
    }
}
