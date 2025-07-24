<?php
// src/Controller/ProductController.php

namespace App\Controller;

use App\Entity\Model;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/products', name: 'product_list')]
    public function index(
        Request $request,
        EntityManagerInterface $em,
        PaginatorInterface $paginator
    ): Response {
        $queryBuilder = $em->getRepository(Model::class)->createQueryBuilder('m');

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            6 // nombre de produits par page
        );

        return $this->render('product/product.html.twig', [
            'models' => $pagination,
        ]);
    }
}
