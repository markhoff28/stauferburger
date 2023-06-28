<?php

namespace App\Controller;

use App\Entity\Meal;
use App\Entity\Order;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

class OrderController extends AbstractController
{
    #[Route('/order', name: 'order')]
    public function index(OrderRepository $orderRepository): Response
    {
        $orders = $orderRepository->findBy([
            'desk' => 'tisch1'
        ]);
        return $this->render('order/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/ordering/{id}', name: 'ordering')]
    public function order(
        Meal $meal,
        ManagerRegistry $doctrine
    ): Response {
        $order = new Order();
        $entityManager = $doctrine->getManager();

        $order->setDesk("tisch1");
        $order->setName($meal->getName());
        $order->setOrderNumber($meal->getId());
        $order->setPrice($meal->getPrice());
        $order->setStatus("offen");

        $entityManager->persist($order);
        $entityManager->flush();

        $this->addFlash('bestell', $order->getName() .' wurde der Bestellung hinzugefügt');

        return $this->redirect(
            $this->generateUrl('menu')
        );
    }

    #[Route('/status/{id},{status}', name: 'status')]
    public function status($id, $status, ManagerRegistry $doctrine) {
        $entityManager = $doctrine->getManager();
        $order = $entityManager->getRepository(Order::class)->find($id);

        $order->setStatus($status);
        $entityManager->flush();

        return $this->redirect($this->generateUrl('order'));
    }
    
    #[Route('/delete/{id}', name: 'delete')]
    public function deleteMeal(
        $id,
        OrderRepository $orderRepository,
        ManagerRegistry $doctrine
    ) {
        $entityManager = $doctrine->getManager();
        $order = $orderRepository->find($id);
        $entityManager->remove($order);
        $entityManager->flush();

        return $this->redirect(
            $this->generateUrl('order')
        );
    }
}
