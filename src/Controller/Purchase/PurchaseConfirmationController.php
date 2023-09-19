<?php 

namespace App\Controller\Purchase;

use App\Cart\CartService;
use App\Entity\Purchase;
use App\Form\CartConfirmationType;
use App\Purchase\PurchasePersister;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PurchaseConfirmationController extends AbstractController
{
    public function __construct(
        protected CartService $cartService,
        protected EntityManagerInterface $em,
        protected PurchasePersister $purchasePersister)
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
                
        // 4. Si il n'y a pas de produits dans mon panier dégager (cartService)
        $cartItems = $this->cartService->getDetailedCartItems();
        if(count($cartItems) ===0){
            $this->addFlash('warning', 'Vous ne pouvez pas confirmer une commande avec une panier vide');
            return $this->redirectToRoute('cart_show');
        }
        // 5. Nous aller creer une purchase
        /** @var Purchase */
        $purchase = $form->getData();

        $this->purchasePersister->storePurchase($purchase);

        //$this->cartService->empty();

        //$this->addFlash('success', 'La commande a bien été enregistré');

        return $this->redirectToRoute('purchase_payment_form', [
            'id' => $purchase->getId()
        ]);

    }
}