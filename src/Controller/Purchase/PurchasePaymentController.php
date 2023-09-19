<?php 

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Repository\PurchaseRepository;
use App\Stripe\StripeService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchasePaymentController extends AbstractController
{
    #[Route("/purchase/pay/{id}", name: "purchase_payment_form")]
    #[IsGranted("ROLE_USER")]
    public function showCardForm($id, PurchaseRepository $purchaseRepository, StripeService $stripeService)
    {
        $purchase = $purchaseRepository->find($id);

        if(!$purchase || ($purchase && ($purchase->getUser() != $this->getUser() || $purchase->getStatus() === Purchase::STATUS_PAID) )){
            return $this->redirectToRoute('cart_show');
        }

        $intent = $stripeService->getPaymentIntent($purchase);

        return $this->render('purchase/payment.html.twig',[
            'clientSecret' => $intent->client_secret,
            'purchase' => $purchase,
            'stripePublicKey' => $stripeService->getPublicKey()
        ]);
    }
}