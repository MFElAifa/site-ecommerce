<?php

namespace App\Controller;

use App\Form\LoginType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'security_login')]
    public function login(AuthenticationUtils $utils, FormFactoryInterface $factory)
    {
        $form = $this->createForm(LoginType::class, [
            'email' => $utils->getLastUsername()
        ]);
        // $form = $factory->createNamed('', LoginType::class, [
        //         '_username' => $utils->getLastUsername()
        //     ]);
        $data = [
            'formView' => $form->createView(),
            'error' => $utils->getLastAuthenticationError()
        ];
        dump($data);
        return $this->render('security/login.html.twig',$data);
    }

    #[Route('/logout', name: 'security_logout')]
    public function logout()
    {
    }
}
