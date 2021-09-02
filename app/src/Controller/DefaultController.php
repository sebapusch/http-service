<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    public function __construct()
    {

    }

    public function index()
    {
        return $this->render('index.html.twig');
    }
}