<?php


namespace App\Service;

use App\Entity\Product;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CheckOwnershipService
{

    public function __construct(TokenStorageInterface $token_storage)
    {
        $this->token_storage = $token_storage;
    }

    public function isProductOwner(Product $product): bool
    {
        return $product->getUser()->getId() === $this->token_storage->getToken()->getUser()->getId();
    }

    public function isCorrectUser(User $user): bool
    {
        return $user->getId() === $this->token_storage->getToken()->getUser()->getId();
    }
}