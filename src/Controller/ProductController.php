<?php

namespace App\Controller;

use App\Entity\Product;
use App\Event\ProductViewEvent;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ProductController extends AbstractController
{
    #[Route('/{slug}', name: 'product_category', priority:-1)]
    public function category(string $slug, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBy(['slug' => $slug]);
        
        if(!$category){
            //throw new NotFoundHttpException("La catégorie demandée n'existe pas");
            throw $this->createNotFoundException("La catégorie demandée n'existe pas");
        }

        return $this->render('product/category.html.twig', [
            'slug' => $slug,
            'category' => $category
        ]);
    }

    #[Route("/{category_slug}/{slug}", name:"product_show", priority:-1)]
    public function show($slug, ProductRepository $productRepository, EventDispatcherInterface $dispatcher)
    {
        $product = $productRepository->findOneBy(['slug' => $slug]);

        if(!$product){
            throw $this->createNotFoundException("Le produit demandé n'existe pas");
        }
        // Event 
        
        $productEvent = new ProductViewEvent($product);
        $dispatcher->dispatch($productEvent, 'product.view');

        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }

    #[Route("/admin/product/create", name:"product_create")]
    public function create(Request $request, SluggerInterface $slugger, EntityManagerInterface $em)
    {
        // $builder = $factory->createBuilder(ProductType::class);
        // $form = $builder->getForm();

        $form = $this->createForm(ProductType::class);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) { 
            $product = $form->getData();
            $product->setSlug(strtolower($slugger->slug($product->getName())));
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getName(),
                'slug' => $product->getSlug()
            ]);
        }
        $formView = $form->createView();
        return $this->render('product/create.html.twig', [
            'formView' => $formView
        ]);
    }

    #[Route("/admin/product/{id}/edit", name:"product_edit")]
    public function edit($id, ProductRepository $productRepository,Request $request, 
        SluggerInterface $slugger, EntityManagerInterface $em)
    {
        $product = $productRepository->find($id);

        $form = $this->createForm(ProductType::class, $product);
        //$form->setData($product);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $product->setSlug(strtolower($slugger->slug($product->getName())));
            $em->flush();

            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getName(),
                'slug' => $product->getSlug()
            ]);
        }

        $formView = $form->createView();

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'formView' => $formView
        ]);
    }

}
