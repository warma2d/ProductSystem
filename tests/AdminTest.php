<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use ProductSystem\Core\Repository\AdminRepository;
use ProductSystem\Core\Repository\ManagerRepository;
use ProductSystem\Core\Service\AdminCreator;
use ProductSystem\Core\Service\ManagerCreator;

final class AdminTest extends TestCase
{
    private AdminRepository $adminRepo;

    public function __construct()
    {
        parent::__construct();
        $this->adminRepo = new AdminRepository();
    }

    public function testCreateAdmin(): void
    {
        $data = [
            'name' => 'Админ',
            'surname' => 'Админов',
            'patronymic' => 'Администратович',
            'email' => 'admin@mail.ru',
            'password' => '1234',
        ];
        $admin = (new AdminCreator())->createOne($data);
        $admin = $this->adminRepo->save($admin);

        $this->assertIsNumeric($admin->getId());
    }
}
