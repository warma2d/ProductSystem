<?php

namespace ProductSystem\Secondary\Controllers;

use Bramus\Router\Router;
use Jasny\Auth\Auth;
use ProductSystem\Core\Repository\ManagerRepository;
use ProductSystem\Core\Repository\ProductRepository;
use ProductSystem\Core\Service\ManagerCreator;
use ProductSystem\Core\Service\ManagerValidator;
use ProductSystem\Core\Service\UriHandler;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Waavi\Sanitizer\Sanitizer;

class AdminController extends AbstractController {

    private Environment $twig;

    private const FILTERS = [
            'name' => 'trim|escape|capitalize',
            'surname' => 'trim|escape|capitalize',
            'patronymic' => 'trim|escape|lowercase',
            'email' => 'trim|escape|lowercase',
            'password' => 'trim|escape',
        ];

    private ManagerRepository $managerRepo;

    private Router $router;
    private Auth $auth;

    public function __construct(Router $router, Auth $auth)
    {
        parent::__construct();

        $this->router = $router;
        $this->auth = $auth;

        $loader = new FilesystemLoader('./Secondary/Pages');
        $this->twig = new Environment($loader);
        $this->managerRepo = new ManagerRepository();

        $this->router->get('/', [$this, 'homeAdminPage']);
        $this->router->get('/logout', [$this, 'logout']);
        $this->router->get('/', [$this, 'homeAdminPage']);
        $this->router->get('/manager/create', [$this, 'createManagerForm']);
        $this->router->post('/manager/create', [$this, 'createManagerHandler']);
        $this->router->get('/managers', [$this, 'managerList']);
        $this->router->get('/manager/\d*/delete', [$this, 'deleteManagerHandler']);
        $this->router->get('/products', [$this, 'productList']);
    }

    public function logout(): void
    {
        $this->auth->logout();
        header('Location: /');
    }

    public function homeAdminPage(): void
    {
        echo $this->twig->render('admin/home.html.twig', [
            'errors' => $this->sessionStorage->getErrors(),
            'post' => $this->sessionStorage->getPostData()
        ]);
    }

    public function createManagerForm(): void
    {
        echo $this->twig->render('admin/create_manager.html.twig', [
            'errors' => $this->sessionStorage->getErrors(),
            'post' => $this->sessionStorage->getPostData()
        ]);
    }

    public function createManagerHandler()
    {
        $sanitizer  = new Sanitizer($_POST, self::FILTERS);
        $inputData = $sanitizer->sanitize();
        $inputData['atCreated'] = new \DateTime();
        $manager = (new ManagerCreator())->createOne($inputData);
        $errors = ManagerValidator::validate($manager);

        if ($errors) {
            $this->sessionStorage->saveErrors($errors);
            $this->sessionStorage->savePostData($_POST);
            header('Location: /manager/create');
        } else {
            $this->sessionStorage->savePostData([]);
            $this->sessionStorage->saveMessage('Менеджер успешно создан');
            $this->managerRepo->save($manager);
            header('Location: /managers/');
        }
    }

    public function managerList()
    {
        $managers = $this->managerRepo->findAllActive();
        echo $this->twig->render('admin/manager_list.html.twig', [
            'message' => $this->sessionStorage->getMessage(),
            'managers' => $managers
        ]);
    }

    public function deleteManagerHandler()
    {
        $id = UriHandler::parseId($this->router->getCurrentUri());
        $this->managerRepo->deleteById($id);
        header('Location: /managers/');
    }

    public function productList()
    {
        $products = (new ProductRepository())->getAllInterfaceProducts();
        echo $this->twig->render('admin/product_list.html.twig', [
            'message' => $this->sessionStorage->getMessage(),
            'errors' => $this->sessionStorage->getErrors(),
            'products' => $products
        ]);
    }
}
