<?php

namespace App\Controller;

use App\Repository\MealRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuController extends AbstractController
{
    #[Route('/menu', name: 'menu')]
    public function menu(MealRepository $mealRepository): Response
    {
        $meals = $mealRepository->findAll();

        return $this->render('menu/index.html.twig', [
            'meals' => $meals,
        ]);
    }
}
