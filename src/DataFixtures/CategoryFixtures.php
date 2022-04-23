<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends BaseFixture
{
    public function loadData(ObjectManager $manager)
    {
        $this->createMany(Category::class, '', 1000, function (Category $category, $count) {
            $category->setName($this->faker->text(100));
        });

        $manager->flush();
    }
}