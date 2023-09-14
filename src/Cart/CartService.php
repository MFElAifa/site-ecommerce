<?php

namespace App\Cart;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Cart\CartItem;

class CartService
{
    protected $session;
    protected $productRepository;

    public function __construct(RequestStack $request, ProductRepository $productRepository)
    {
        $this->session = $request->getSession();
        $this->productRepository = $productRepository;
    }

    public function getCart(): array
    {
        return $this->session->get('cart', []);
    }

    public function saveCart(array $cart)
    {
        $this->session->set('cart', $cart);
    }

    public function add(int $id){
        // 1. Retourver le panier dans la session (sous forme de tableau)
        // 2. Si il n'existe pas encore, alors prondre un tableau vide
        $cart = $this->getCart();
        
        // 3. Voir si le produit ($id) existe déjà dans le tableau
        // 4. Si c'est le cas, simplement augmenter la quantité
        // 5. Sinon, ajouter le produit avec la quantité 1
        if(!array_key_exists($id, $cart)){
            $cart[$id] = 0;
        }
        
        $cart[$id]++;
        
        // 6. Enregistrer le tableau mis à jour dans la session
        $this->saveCart($cart);
    }
    
    public function getTotal(): int
    {
        $total = 0;

        foreach($this->getCart() as $id => $qty){
            $product = $this->productRepository->find($id);
            if(!$product){
                continue;
            }
            $total += $product->getPrice() * $qty;
        }

        return $total;
    }

    public function getDetailedCartItems(): array
    {
        $detailedCart = [];
        foreach($this->getCart() as $id => $qty){
            $product = $this->productRepository->find($id);
            
            if(!$product){
                continue;
            }

            $detailedCart[] = new CartItem($product, $qty);
        }

        return $detailedCart;
    }

    public function remove(int $id)
    {
        $cart = $this->getCart();
        
        unset($cart[$id]);

        $this->saveCart($cart);
    }

    public function decrement(int $id)
    {
        $cart = $this->getCart();
        
        if(!array_key_exists($id, $cart)){
            return;
        }

        
        if($cart[$id] == 1){
            $this->remove($id);
            return;
        }

        $cart[$id]--;

        $this->saveCart($cart);
    }
}