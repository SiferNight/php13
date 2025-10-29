<?php

namespace App\Controller;

use App\Entity\Book;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;

final class BookController extends AbstractController
{
    private $bookRepository;
    private $entityManager;

    public function __construct(BookRepository $bookRepository, EntityManagerInterface $entityManager)
    {
        $this->bookRepository = $bookRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/book', name: 'app_book')]
    public function index(EntityManagerInterface $entityManager): Response
    {

        $samdorog = $entityManager->createQueryBuilder()
            ->select('b.id,b.title, b.price')
            ->from(Book::class, 'b')
            ->orderBy('b.price', 'DESC')
            ->setMaxResults(1)

            ->getQuery()
            ->getOneOrNullResult();

        return $this->render('book/samdorog.html.twig', [
            'book' => $samdorog
        ]);
    }
}
