<?php

namespace App\Controller;

use App\Cart\CartService;
use App\Form\CartConfirmationType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class CartController extends AbstractController
{

    public function __construct(protected ProductRepository $productRepository, protected CartService $cartService)
    {
        
    }

    #[Route('/cart/add/{id}', name: 'cart_add', requirements: ["id" => "\d+"])]
    public function add($id, Request $request): Response
    {
        // 0. Securisation: est-ce-que le produit existe
        $product = $this->productRepository->find($id);

        if(!$product){
            throw $this->createNotFoundException("Le produit $id n'existe pas");
        }

        $this->cartService->add($id);

        $this->addFlash('success', "Le produit a bien été ajouté au panier");
        // $flasheBag->add('info', "Une petite information");
        // $flasheBag->add('warning', "Attention !");
        // $flasheBag->add('success', "Un autre succès");
        if($request->query->get('returnToCart')){
            return $this->redirectToRoute('cart_show');
        }
        return $this->redirectToRoute('product_show', [
            'category_slug' => $product->getCategory()->getSlug(),
            'slug' => $product->getSlug()
        ]);
    }

    #[Route('/cart', name: 'cart_show')]
    public function show()
    {
        $form = $this->createForm(CartConfirmationType::class);

        $detailedCart = $this->cartService->getDetailedCartItems();
        $total = $this->cartService->getTotal();
        return $this->render('cart/index.html.twig', [
            'items' => $detailedCart,
            'total' => $total,
            'confirmationForm' => $form->createView()
        ]);
    }

    #[Route('/cart/delete/{id}', name: 'cart_delete', requirements: ["id" => "\d+"])]
    public function delete($id): Response
    {
        $product = $this->productRepository->find($id);

        if(!$product){
            throw $this->createNotFoundException("Le produit $id n'existe pas et ne peut pas être supprimé !");
        }

        $this->cartService->remove($id);

        $this->addFlash("success", "Le produit a bien été supprimé du panier !");

        return $this->redirectToRoute("cart_show");
    }

    #[Route('/cart/decrement/{id}', name: 'cart_decrement', requirements: ["id" => "\d+"])]
    public function decrement($id): Response
    {
        $product = $this->productRepository->find($id);

        if(!$product){
            throw $this->createNotFoundException("Le produit $id n'existe pas et ne peut pas être décrémenté !");
        }

        $this->cartService->decrement($id);

        $this->addFlash("success", "Le produit a bien été décrémenté !");
        
        return $this->redirectToRoute("cart_show");
    }
}
