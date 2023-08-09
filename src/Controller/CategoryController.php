<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoryController extends AbstractController
{

    public function __construct(protected CategoryRepository $categoryRepository)
    {
        
    }

    public function renderMenuList()
    {
        // 1. Aller chercher les catégories dans la base de données
        $categories = $this->categoryRepository->findAll();

        // 2. Renvoyer le rendu HTML sous la forme d'une Response ($this->render)
        return $this->render('category/_menu.html.twig',[
            'categories' => $categories
        ]);
    }


    #[Route('/admin/category/create', name: 'category_create')]
    public function create(Request $request, SluggerInterface $slugger, EntityManagerInterface $em): Response
    {
        $category = new Category;
        $form = $this->createForm(CategoryType::class, $category);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) { 
            $category->setSlug(strtolower($slugger->slug($category->getName())));
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        $formView = $form->createView();

        return $this->render('category/create.html.twig', [
            'formView' => $formView
        ]);
    }

    #[Route('/admin/category/{id}/edit', name: 'category_edit')]
    //#[IsGranted('ROLE_ADMIN', message:"Vous n'avez pas le droit d'accéder à cette ressource")]
    //#[IsGranted('CAN_EDIT', subject:"id", message:"Vous n'êtes pas le propriétaire de cette catégorie!!")]
    public function edit($id, CategoryRepository $categoryRepository,Request $request, SluggerInterface $slugger, EntityManagerInterface $em, Security $security): Response
    {
        // $this->denyAccessUnlessGranted("ROLE_ADMIN",null, "Vous n'avez pas le droit d'accéder à cette ressource");
        
        // $user = $this->getUser();
        
        // if($user === null){
        //     return $this->redirectToRoute('security_login');
        // }

        // if($this->isGranted('ROLE_ADMIN') === false){
        //     throw new AccessDeniedHttpException("Vous n'avez pas le droit d'accéder à cette ressource");
        // }

        $category = $categoryRepository->find($id);

        if(!$category){
            throw new NotFoundHttpException("Cette Catégorie n'existe pas");
        }

        // $user = $this->getUser();
        
        // if($user === null){
        //     return $this->redirectToRoute('security_login');
        // }

        // if($category->getOwner() !== $user){
        //     throw new AccessDeniedHttpException("Vous n'êtes pas le propriétaire de cette catégorie");
        // }

        // $security->isGranted('CAN_EDIT', $category);

        // $this->denyAccessUnlessGranted('CAN_EDIT', $category, "Vous n'êtes pas le propriétaire de cette catégorie!");
    
        $form = $this->createForm(CategoryType::class, $category);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) { 
            $category->setSlug(strtolower($slugger->slug($category->getName())));
            $em->flush();

            return $this->redirectToRoute('homepage');
        }
        
        $formView = $form->createView();

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'formView' => $formView
        ]);
    }
}
