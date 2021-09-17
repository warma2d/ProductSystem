<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use ProductSystem\Core\Exceptions\ApplicationException;
use ProductSystem\Core\Model\Product\Product;
use ProductSystem\Core\Repository\ProductRepository;
use ProductSystem\Core\Service\ProductCreator;

final class ProductTest extends TestCase
{
    private ProductRepository $productRepo;

    public function __construct()
    {
        parent::__construct();
        $this->productRepo = new ProductRepository();
    }

    public function testCreateProduct(): void
    {
        $data = [
            'name' => 'Table',
            'price' => '13.77'
        ];

        $table = (new ProductCreator())->createOne($data);
        $table = $this->productRepo->save($table);

        $foundTable = $this->productRepo->findById($table->getId());

        $this->assertEquals(
            $table->getName(),
            $foundTable->getName()
        );

        $this->assertEquals(
            $table->getPrice(),
            $foundTable->getPrice()
        );
    }

    public function testDeleteProduct(): void
    {
        $data = [
            'name' => 'Gold Pencil',
            'price' => '150.99'
        ];

        $pencil = (new ProductCreator())->createOne($data);
        $pencil = $this->productRepo->save($pencil);

        $foundPencil = $this->productRepo->findById($pencil->getId());

        $this->productRepo->delete($foundPencil);

        $deletedPencil = $this->productRepo->findById($foundPencil->getId());

        $this->assertNull($foundPencil->getAtDeleted());
        $this->assertTrue(!!$deletedPencil->getAtDeleted());
    }
}
