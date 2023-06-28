<?php

namespace App\Controller;

use App\Entity\Meal;
use App\Form\MealType;
use App\Repository\MealRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

#[Route('/meal', name: 'meal.')]
class MealController extends AbstractController
{
    #[Route('/', name: 'edit')]
    public function index(MealRepository $mealRepository): Response
    {
        $meals = $mealRepository->findAll();

        return $this->render('meal/index.html.twig', [
            'meals' => $meals,
        ]);
    }

    #[Route('/create', name: 'create')]
    public function createMeal(
        Request $request,
        ManagerRegistry $doctrine
    ):Response {
        $meal = new Meal();

        // form
        $form = $this->createForm(MealType::class, $meal);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $entityManager = $doctrine->getManager();
            
            $image = $request->files->get('meal')['image'];

            if($image) {
                $filename = md5(uniqid()). '.' . $image->guessClientExtension();
            }

            $image->move(
                $this->getParameter('images_folder'),
                $filename
            );

            $meal->setImage($filename);

            $entityManager->persist($meal);
            $entityManager->flush();

            return $this->redirect(
                $this->generateUrl('meal.edit')
            );
        }

        return $this->render('meal/create.html.twig', [
            'createForm' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function deleteMeal(
        $id,
        MealRepository $mealRepository,
        ManagerRegistry $doctrine
    ) {
        $entityManager = $doctrine->getManager();
        $meal = $mealRepository->find($id);
        $entityManager->remove($meal);
        $entityManager->flush();

        // message
        $this->addFlash('erfolg', 'Gericht mit der ID ' . $id .' gelöscht');

        return $this->redirect(
            $this->generateUrl('meal.edit')
        );
    }

    #[Route('/show/{id}', name: 'show')]
    public function showMeal(Meal $meal)
    {
        return $this->render('meal/show.html.twig', [
            'meal' => $meal,
        ]);
    }
    
    #[Route('/price/{id}', name: 'price')]
    public function price(
        $id,
    MealRepository $mealRepository
    ) {
        $meal = $mealRepository->find5EuroMeal($id);
        
        dump($meal);
    }
}
