<?php 

namespace App\EventDispatcher;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use App\Event\PurchaseSuccessEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mime\Address;

class PurchaseSuccessEmailSubscriber implements EventSubscriberInterface
{
    public function __construct(protected LoggerInterface $logger, protected MailerInterface $mailer, protected Security $security)
    {
        
    }
    public static function getSubscribedEvents()
    {
        return [
            'purchase.success' => 'sendSuccessEmail'
        ];
    }

    public function sendSuccessEmail(PurchaseSuccessEvent $purchaseSuccessEvent){
        // 1. recuperer le user (Security)
        /** @var User */
        $currentUser = $this->security->getUser();
        
        // 2. recuperer la commande ($purchaseSuccessEvent)
        $purchase = $purchaseSuccessEvent->getPurchase();
        
        // 3. ecrire le mail (TemplatedEmail)
        $email = new TemplatedEmail();
        $email->to(new Address($currentUser->getEmail(), $currentUser->getFullname()))
            ->from("contact@mail.com")
            ->subject("Bravo, votre commande ({$purchase->getId()}) a bien été confirmée")
            ->htmlTemplate("emails/purchase_success.html.twig")
            ->context([
                'purchase' => $purchase,
                'user' => $currentUser
            ]);
        
        // 4. envoyer le mail (mailer)
        $this->mailer->send($email);
        
        $this->logger->info("Email envoyé pour la commande n° : ".$purchaseSuccessEvent->getPurchase()->getId());
    }
}