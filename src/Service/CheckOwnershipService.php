<?php


namespace App\Service;

use App\Entity\Product;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class CheckOwnershipService
 *
 * @package App\Service
 */
class CheckOwnershipService
{
    /**
     * @var TokenStorageInterface
     */
    private $token_storage;
    /**
     * CheckOwnershipService constructor.
     *
     * @param TokenStorageInterface $token_storage
     */
    public function __construct(TokenStorageInterface $token_storage)
    {
        $this->token_storage = $token_storage;
    }

    /**
     * @param Product $product
     * @return bool
     */
    public function isProductOwner(Product $product): bool
    {
        return $product->getUser()->getId() === $this->token_storage->getToken()->getUser()->getId();
    }

    /**
     * @param User $user
     * @return bool
     */
    public function isCorrectUser(User $user): bool
    {
        return $user->getId() === $this->token_storage->getToken()->getUser()->getId();
    }
}
