<?php

namespace ProductSystem\Secondary\Controllers;

use Bramus\Router\Router;
use Jasny\Auth\Auth;
use ProductSystem\Core\Exceptions\ApplicationException;
use ProductSystem\Core\Repository\ProductRepository;
use ProductSystem\Core\Repository\SetRepository;
use ProductSystem\Core\Service\ProductCreator;
use ProductSystem\Core\Service\ProductValidator;
use ProductSystem\Core\Service\SetCreator;
use ProductSystem\Core\Service\SetValidator;
use ProductSystem\Core\Service\UriHandler;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Waavi\Sanitizer\Sanitizer;

class ManagerController extends AbstractController
{

    private const FILTERS = [
        'name' => 'trim|escape|capitalize',
        'price' => 'trim|digit',
    ];

    private Environment $twig;
    private Router $router;
    private ProductRepository $productRepo;
    private SetRepository $setRepo;
    private Auth $auth;

    public function __construct(Router $router, Auth $auth)
    {
        parent::__construct();

        $this->router = $router;
        $this->auth = $auth;

        $loader = new FilesystemLoader('./Secondary/Pages');
        $this->twig = new Environment($loader);
        $this->productRepo = new ProductRepository();
        $this->setRepo = new SetRepository();

        $this->router->get('/', [$this, 'homeManagerPage']);
        $this->router->get('/logout', [$this, 'logout']);
        $this->router->get('/product/create', [$this, 'createProductForm']);
        $this->router->post('/product/create', [$this, 'createProductHandler']);
        $this->router->get('/set/create', [$this, 'createSetForm']);
        $this->router->post('/set/create', [$this, 'createSetHandler']);
        $this->router->get('/products', [$this, 'productList']);
        $this->router->get('/product/\d*/delete', [$this, 'deleteProductHandler']);
        $this->router->get('/set/\d*/delete', [$this, 'deleteSetHandler']);
    }

    public function logout(): void
    {
        $this->auth->logout();
        header('Location: /');
    }

    public function homeManagerPage(): void
    {
        echo $this->twig->render('manager/home.html.twig', [
            'errors' => $this->sessionStorage->getErrors(),
            'post' => $this->sessionStorage->getPostData()
        ]);
    }

    public function createSetForm(): void
    {
        $products = $this->productRepo->getAllInterfaceProducts();

        echo $this->twig->render('manager/create_set.html.twig', [
            'errors' => $this->sessionStorage->getErrors(),
            'post' => $this->sessionStorage->getPostData(),
            'products' => $products
        ]);
    }

    public function createSetHandler()
    {
        $sanitizer = new Sanitizer($_POST, self::FILTERS);
        $inputData = $sanitizer->sanitize();
        $inputData['atCreated'] = new \DateTime();

        $products = $this->productRepo->getInterfaceProductsByInputData($inputData['products']);
        $inputData['components'] = $products;

        $set = (new SetCreator())->createOne($inputData);

        $errors = SetValidator::validate($set);

        if ($errors) {
            $this->sessionStorage->saveErrors($errors);
            $this->sessionStorage->savePostData($_POST);
            header('Location: /set/create');
        } else {
            $this->sessionStorage->savePostData([]);
            $this->sessionStorage->saveMessage('Набор успешно создан');
            $this->setRepo->save($set);
            header('Location: /products/');
        }
    }

    public function createProductForm(): void
    {
        echo $this->twig->render('manager/create_product.html.twig', [
            'errors' => $this->sessionStorage->getErrors(),
            'post' => $this->sessionStorage->getPostData()
        ]);
    }

    public function createProductHandler()
    {
        $sanitizer = new Sanitizer($_POST, self::FILTERS);
        $inputData = $sanitizer->sanitize();
        $inputData['atCreated'] = new \DateTime();
        $product = (new ProductCreator())->createOne($inputData);
        $errors = ProductValidator::validate($product);

        if ($errors) {
            $this->sessionStorage->saveErrors($errors);
            $this->sessionStorage->savePostData($_POST);
            header('Location: /product/create');
        } else {
            $this->sessionStorage->savePostData([]);
            $this->sessionStorage->saveMessage('Продукт успешно создан');
            $this->productRepo->save($product);
            header('Location: /products/');
        }
    }

    public function productList()
    {
        $products = $this->productRepo->getAllInterfaceProducts();
        echo $this->twig->render('manager/product_list.html.twig', [
            'message' => $this->sessionStorage->getMessage(),
            'errors' => $this->sessionStorage->getErrors(),
            'products' => $products
        ]);
    }

    public function deleteProductHandler()
    {
        $id = UriHandler::parseId($this->router->getCurrentUri());
        try {
            $this->productRepo->deleteById($id);
        } catch (ApplicationException $e) {
            $this->sessionStorage->saveErrors($e->getMessage());
        }
        header('Location: /products/');
    }

    public function deleteSetHandler()
    {
        $id = UriHandler::parseId($this->router->getCurrentUri());
        try {
            $this->setRepo->deleteById($id);
        } catch (ApplicationException $e) {
            $this->sessionStorage->saveErrors($e->getMessage());
        }
        header('Location: /products/');
    }
}
