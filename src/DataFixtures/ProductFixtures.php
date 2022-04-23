<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends BaseFixture implements DependentFixtureInterface
{
    public function loadData(ObjectManager $manager)
    {
        $this->createMany(Product::class, '', 1000, function (Product $product, $count) {
            $product->setName($this->faker->text(200));
            $product->setDescription($this->faker->paragraph(150));
            $product->setCategory($this->getRandomReference(Category::class));
        });

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CategoryFixtures::class,
        ];
    }
}