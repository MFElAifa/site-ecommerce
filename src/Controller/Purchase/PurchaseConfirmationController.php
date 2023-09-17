<?php 

namespace App\Controller\Purchase;

use App\Cart\CartService;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use App\Form\CartConfirmationType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PurchaseConfirmationController extends AbstractController
{
    public function __construct(
        protected CartService $cartService,
        protected EntityManagerInterface $em)
    {
    }
    #[Route("/purchase/confirm", name: "purchase_confirm")]
    #[IsGranted("ROLE_USER", message:"Vous n'êtes pas connecté !")]
    public function confirm(Request $request)
    {
        // 1. Lire les donnees de formulaire
        // formFactoryInterface / Request
        $form = $this->createForm(CartConfirmationType::class);
        $form->handleRequest($request);
        // 2. Si le formulaire n'a pas été soumis dégager
        if(!$form->isSubmitted())
        {
            // Message flash puis redirection (flashBagInterface)
            $this->addFlash(
               'warning', 
               'Vous devez remplir le formulaire de confirmation');
            return $this->redirectToRoute('cart_show');
        }
        // 3. Si je ne suis pas connecté dégager (Security)
        $user = $this->getUser();
        
        // 4. Si il n'y a pas de produits dans mon panier dégager (cartService)
        $cartItems = $this->cartService->getDetailedCartItems();
        if(count($cartItems) ===0){
            $this->addFlash('warning', 'Vous ne pouvez pas confirmer une commande avec une panier vide');
            return $this->redirectToRoute('cart_show');
        }
        // 5. Nous aller creer une purchase
        /** @var Purchase */
        $purchase = $form->getData();

        // 6. Nous allons lier l'utilisateur connecte (Security)
        $purchase->setUser($user)
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

        $this->cartService->empty();

        $this->addFlash('success', 'La commande a bien été enregistré');

        return $this->redirectToRoute('purchase_index');

    }
}