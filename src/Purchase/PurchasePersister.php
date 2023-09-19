<?php 

namespace App\Purchase;

use DateTime;
use App\Entity\Purchase;
use App\Cart\CartService;
use App\Entity\PurchaseItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class PurchasePersister
{
    public function __construct(
        protected EntityManagerInterface $em, 
        protected CartService $cartService,
        protected Security $security)
    {
        
    }

    public function storePurchase(Purchase $purchase)
    {
        // 6. Nous allons lier l'utilisateur connecte (Security)
        $purchase->setUser($this->security->getUser())
            ->setPurchasedAt(new DateTime())
            ->setTotal($this->cartService->getTotal());

        $this->em->persist($purchase);
        // 7. Nous allons la lier avec les produits qui sont dans le panier (cartService)
        foreach($this->cartService->getDetailedCartItems() as $cartItem){
            $purchaseItem = new PurchaseItem();
            $purchaseItem->setPurchase($purchase)
                ->setProduct($cartItem->getProduct())
                ->setProductName($cartItem->getProduct()->getName())
                ->setProductPrice($cartItem->getProduct()->getPrice())
                ->setQuantity($cartItem->getQty())
                ->setTotal($cartItem->getTotal());
        
            $this->em->persist($purchaseItem);
        }

        // 8. Nous allons enregistrer la commande (EntityManagerInterface)
        $this->em->flush();
    }
}