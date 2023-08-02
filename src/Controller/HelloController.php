<?php


namespace App\Controller;

use Twig\Environment;
use App\Taxes\Detector;
use App\Taxes\Calculator;
use Cocur\Slugify\Slugify;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HelloController extends AbstractController
{
    protected $logger;
    protected $calculator;
    protected $slugify;
    public function __construct(LoggerInterface $logger, Calculator $calculator, Slugify $slugify, protected Detector $detctor)
    {
        $this->logger = $logger;
        $this->calculator = $calculator;
        $this->slugify = $slugify;
    }

    #[Route("/hello/{name?World}", name:"hello")]
    public function hello(string $name): Response
    {
        return $this->render('hello.html.twig', [
            'name' => $name
        ]);
    }
}