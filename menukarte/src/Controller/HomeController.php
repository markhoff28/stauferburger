<?php

namespace App\Controller;

use App\Repository\MealRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(MealRepository $mealRepository): Response
    {
        $meals = $mealRepository->findAll();

        $randomMeals = array_rand($meals, 2);

        return $this->render('home/index.html.twig', [
            'meal0' => $meals[$randomMeals[0]],
            'meal1' => $meals[$randomMeals[1]],
        ]);
    }
}
