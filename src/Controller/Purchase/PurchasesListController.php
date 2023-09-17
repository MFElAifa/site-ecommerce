<?php 

namespace App\Controller\Purchase;

use App\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Twig\Environment;

class PurchasesListController extends AbstractController
{

    #[Route("/purshases", name: "purchase_index")]
    #[IsGranted("ROLE_USER", message:"Vous devez être connecté pour accéder à vos commandes")]
    public function index()
    {
        // 1. Nous devons nous assurer que la personne est connecté (single page d'accueil) 

        /** @var User */
        //$user = $this->security->getUser();
        $user = $this->getUser();

        
        // 2. Nous voulons savoir QUI est connecté
        // 3. Nous voulons passer l'utilisateur connecté à Twig afin d'afficher les commandes

        return $this->render('purchase/index.html.twig', [
            'purchases' => $user->getPurchases()
        ]);
    }
}