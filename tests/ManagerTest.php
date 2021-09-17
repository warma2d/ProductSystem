<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use ProductSystem\Core\Repository\ManagerRepository;
use ProductSystem\Core\Service\ManagerCreator;

final class ManagerTest extends TestCase
{
    private ManagerRepository $managerRepo;

    public function __construct()
    {
        parent::__construct();
        $this->managerRepo = new ManagerRepository();
    }

    public function testCreateManager(): void
    {
        $data = [
            'name' => 'Тест',
            'surname' => 'Иванов',
            'patronymic' => 'Пупкинович',
            'email' => 'test@mail.ru',
            'password' => '1234',
        ];
        $manager = (new ManagerCreator())->createOne($data);
        $manager = $this->managerRepo->save($manager);

        $this->assertIsNumeric($manager->getId());
    }

    public function testDeleteManager(): void
    {
        $data = [
            'name' => 'Василий',
            'surname' => 'Пупкин',
            'patronymic' => 'Иванович',
            'email' => 'vasya@mail.ru',
            'password' => '12345',
        ];
        $manager = (new ManagerCreator())->createOne($data);
        $manager = $this->managerRepo->save($manager);

        $this->assertTrue($this->managerRepo->delete($manager));
    }
}
