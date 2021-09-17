<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use ProductSystem\Core\Exceptions\ApplicationException;
use ProductSystem\Core\Model\Product\Product;
use ProductSystem\Core\Model\Product\Set;
use ProductSystem\Core\Repository\ProductRepository;
use ProductSystem\Core\Repository\SetRepository;
use ProductSystem\Core\Service\ProductCreator;
use ProductSystem\Core\Service\SetCreator;

final class SetTest extends TestCase
{
    private SetRepository $setRepo;
    private ProductRepository $productRepo;
    private ProductCreator $productCreator;
    private SetCreator $setCreator;

    public function __construct()
    {
        parent::__construct();
        $this->setRepo = new SetRepository();
        $this->productRepo = new ProductRepository();
        $this->productCreator = new ProductCreator();
        $this->setCreator = new SetCreator();
    }

    public function testCreateSet(): void
    {
        $candy = $this->productCreator->createOne([
            'name' => 'Candy',
            'price' => 1.2,
        ]);

        $chocolate = $this->productCreator->createOne([
            'name' => 'Chocolate',
            'price' => 1.1
        ]);

        $surpriseBox = $this->setCreator->createOne([
            'name' => 'Surprise Box',
            'components' => [
                $candy,
                $chocolate
            ]
        ]);


        $pencil = $this->productCreator->createOne([
            'name' => 'Pencil',
            'price' => 3
        ]);

        $eraser = $this->productCreator->createOne([
            'name' => 'Eraser',
            'price' => 2.5
        ]);

        $set = $this->setCreator->createOne([
            'name' => 'Young schoolboy set',
            'components' => [
                $pencil,
                $eraser,
                $surpriseBox
            ]
        ]);
        $savedSet = $this->setRepo->save($set);

        $this->assertEquals((1.2+1.1+3+2.5), $savedSet->getPrice());
    }

    public function testDeleteSetByManager(): void
    {
        $unattachedPen = $this->productCreator->createOne([
            'name' => 'Pen',
            'price' => 1
        ]);
        $unattachedPen = $this->productRepo->save($unattachedPen);

        $pencil = $this->productCreator->createOne([
            'name' => 'Mini Pencil',
            'price' => 2
        ]);
        $pencil = $this->productRepo->save($pencil);

        $pencilSet = $this->setCreator->createOne([
            'name' => 'Pencil set',
            'components' => [
                $pencil
            ]
        ]);
        $pencilSet = $this->setRepo->save($pencilSet);

        try {
            $this->productRepo->delete($pencil);
        } catch (ApplicationException $e) {
            print $e->getMessage()."\r\n";
            $this->assertNotEmpty($e->getMessage());
        }

        try {
            $this->setRepo->delete($pencilSet);
        } catch (ApplicationException $e) {
            print $e->getMessage()."\r\n";
            $this->assertNotEmpty($e->getMessage());
        }

        $this->productRepo->delete($unattachedPen);
    }
}
