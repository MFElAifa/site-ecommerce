<?php 

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Cart\CartService;
use App\Event\PurchaseSuccessEvent;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PurchasePaymentSuccessController extends AbstractController
{
    public function __construct(
        protected CartService $cartService,
        protected EntityManagerInterface $em)
    {
    }
    
    #[IsGranted("ROLE_USER")]
    #[Route("/purchase/finish/{id}", name: "purchase_payment_success")]
    public function success($id, PurchaseRepository $purchaseRepository, EventDispatcherInterface $dispatcher)
    {
        $purchase = $purchaseRepository->find($id);

        // erreur
        if(!$purchase || ($purchase && ($purchase->getUser() != $this->getUser() || $purchase->getStatus() === Purchase::STATUS_PAID) )){
            $this->addFlash("warning", "La commande n'existe pas !");
            return $this->redirectToRoute('purchase_index');
        }

        // changer le status
        $purchase->setStatus(Purchase::STATUS_PAID);
        $this->em->flush($purchase);

        // vider le panier
        $this->cartService->empty();

        // Lancer un événement qui permettre aux devs de réagir à la fin d'une commande
        $purchaseEvent = new PurchaseSuccessEvent($purchase);
        $dispatcher->dispatch($purchaseEvent, 'purchase.success');
        
        // flash
        $this->addFlash("success", "La commande a été payé et confirmé");
        return $this->redirectToRoute('purchase_index');
    }
}