<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Common\Persistence\ObjectManager;

class ProductFixtures extends BaseFixtures
{
    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(Product::class, 10, function (Product $product) {
            $product->setTitle($this->faker->company());
            $product->setSupplierId(random_int(1, 190));
            $product->setDeadline($this->faker->dateTimeBetween('+1 days', '+50 days'));
            $product->setPicture($this->faker->imageUrl(640, 480, 'cats'));
            $product->setDescription($this->faker->text(400));
            $product->setStatus($this->faker->boolean(60));
            $product->setCreatedAt($this->faker->dateTimeBetween('-20 days', '-10 days'));
            $product->setUpdatedAt($this->faker->dateTimeBetween('-10 days', '-1 days'));
        });

        $manager->flush();
    }
}





