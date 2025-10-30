<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Entity\Client;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;

final class ClientController extends AbstractController
{
    private  ClientRepository $clientRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(ClientRepository $clientRepository, EntityManagerInterface $entityManager)
    {
        $this->clientRepository = $clientRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/topclients', name: 'app_client_top')]
    public function clientsTopList(EntityManagerInterface $entityManager): Response
    {
        $topklienti = $entityManager->createQueryBuilder()
            
            ->select(
                'c.name',
                'c.email',
                'SUM(oi.quantity * oi.unitPrice) as totalSpent'
            )

            ->from(Client::class, 'c')
            ->leftJoin('c.orders', 'o')         
            ->leftJoin('o.orderItems', 'oi')  
            ->groupBy('c.id')
            ->orderBy('totalSpent', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();


        return $this->render('client/topklienti.html.twig', [
            'clients' => $topklienti,
        ]);
    }

    #[Route('/client', name: 'app_client')]
    public function spisoklientov(EntityManagerInterface $entityManager): Response
    {
        $klientizakaz = $entityManager->createQueryBuilder()
            ->select('u.id, u.name, u.email, COUNT(o.id) as ordersCount')
            ->from(Client::class, 'u')
            ->leftJoin('u.orders', 'o')
            ->groupBy('u.id')
            ->orderBy('ordersCount', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('client/spisok.html.twig', [
            'clients' => $klientizakaz,
        ]);
    }
}
