<?php 

namespace App\EventDispatcher;

use Psr\Log\LoggerInterface;
use App\Event\ProductViewEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductViewEmailSubscriber implements EventSubscriberInterface
{

    public function __construct(protected LoggerInterface $logger)
    {
        
    }
    
    public static function getSubscribedEvents(){
        return [
            'product.view' => 'sendEmailToAdmin'
        ];
    }

    public function sendEmailToAdmin(ProductViewEvent $productViewEvent){
        $this->logger->info("Un client vient de voir le produit n° : ".$productViewEvent->getProduct()->getId());
    }

}