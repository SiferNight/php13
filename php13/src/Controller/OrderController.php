<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Order;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\OrderRepository;

final class OrderController extends AbstractController
{
    private $orderRepository;
    private $entityManager;

    public function __construct(OrderRepository $orderRepository, EntityManagerInterface $entityManager)
    {
        $this->orderRepository = $orderRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/orders', name: 'orders_list')]
    public function ordersList(EntityManagerInterface $entityManager): Response
    {
        $sredorders = $entityManager->createQueryBuilder()
            ->select('c.id,
                     c.name as clientName, c.email as clientEmail,
                     SUM(oi.quantity * oi.unitPrice) / COUNT(DISTINCT o.id) as avgOrder') 
            ->from(Client::class, 'c')
            ->leftJoin('c.orders', 'o')
            ->leftJoin('o.orderItems', 'oi') 
            ->groupBy('c.id') 
            ->getQuery()
            ->getResult();

            
        $ordersum = $entityManager->createQueryBuilder()
            ->select('o.id, o.createdAt, 
                     c.name as clientName, c.email as clientEmail,
                     SUM(oi.quantity * oi.unitPrice) as orderTotal') 
            ->from(Order::class, 'o')
            ->leftJoin('o.client', 'c')
            ->leftJoin('o.orderItems', 'oi') 
            ->groupBy('o.id') 
            ->orderBy('o.createdAt', 'DESC') 
            ->getQuery()
            ->getResult();


        return $this->render('order/spisok.html.twig', [
            'orders' => $ordersum,
            'avgOrders'=> $sredorders
        ]);
    }
}
