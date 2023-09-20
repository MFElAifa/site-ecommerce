<?php 

namespace App\EventDispatcher;

use Psr\Log\LoggerInterface;
use App\Event\ProductViewEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mime\Address;

class ProductViewEmailSubscriber implements EventSubscriberInterface
{

    public function __construct(protected LoggerInterface $logger, protected MailerInterface $mailer)
    {
        
    }
    
    public static function getSubscribedEvents(){
        return [
            'product.view' => 'sendEmailToAdmin'
        ];
    }

    public function sendEmailToAdmin(ProductViewEvent $productViewEvent){
        /*//$email = new Email();
        $email = new TemplatedEmail();
        $email->from(new Address("contact@mail.com", "Infos de la boutique"))
            ->to("admin@mail.com")
            ->text("Un visiteur est en train de voir produit n° : ".$productViewEvent->getProduct()->getId())
            //->html("<h1>Visite du produit {$productViewEvent->getProduct()->getId()}</h1>")
            ->htmlTemplate("emails/product_view.html.twig")
            ->context([
                'product' => $productViewEvent->getProduct()
            ])
            ->subject("Visite du produit n° :".$productViewEvent->getProduct()->getId());
        
        $this->mailer->send($email);*/

        $this->logger->info("Un client vient de voir le produit n° : ".$productViewEvent->getProduct()->getId());
    }

}