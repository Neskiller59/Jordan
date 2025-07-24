<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Repository\CartRepository;
use App\Repository\ModelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/panier')]
class CartController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private CartRepository $cartRepository,
        private ModelRepository $modelRepository,
        private Security $security,
    ) {}

    #[Route('/', name: 'panier_afficher')]
    public function index(): Response
    {
        $user = $this->security->getUser();

        if (!$user) {
            $this->addFlash('warning', 'Vous devez être connecté pour accéder au panier.');
            return $this->redirectToRoute('app_login');
        }

        $cart = $this->cartRepository->findOneBy(['user' => $user]) ?? new Cart();
        if (!$cart->getId()) {
            $cart->setUser($user);
            $this->em->persist($cart);
            $this->em->flush();
        }

        return $this->render('cart/cart.html.twig', [
            'cart' => $cart,
        ]);
    }

    #[Route('/ajouter/{id}', name: 'ajouter_au_panier')]
    public function ajouter(int $id): Response
    {
        $user = $this->security->getUser();
        if (!$user) {
            $this->addFlash('warning', 'Vous devez être connecté pour ajouter un produit au panier.');
            return $this->redirectToRoute('app_login');
        }

        $model = $this->modelRepository->find($id);
        if (!$model) {
            $this->addFlash('danger', 'Produit introuvable.');
            return $this->redirectToRoute('product_list');
        }

        $cart = $this->cartRepository->findOneBy(['user' => $user]) ?? new Cart();
        if (!$cart->getId()) {
            $cart->setUser($user);
            $this->em->persist($cart);
        }

        $cartItem = null;
        foreach ($cart->getCartItems() as $item) {
            if ($item->getModel()->getId() === $model->getId()) {
                $cartItem = $item;
                break;
            }
        }

        if ($cartItem) {
            $cartItem->setQuantity($cartItem->getQuantity() + 1);
        } else {
            $cartItem = new CartItem();
            $cartItem->setCart($cart);
            $cartItem->setModel($model);
            $cartItem->setQuantity(1);
            $cartItem->setPriceAtTime($model->getPrix());
            $this->em->persist($cartItem);
            $cart->addCartItem($cartItem);
        }

        $this->em->flush();

        $this->addFlash('success', 'Produit ajouté au panier.');
        return $this->redirectToRoute('panier_afficher');
    }

    #[Route('/supprimer/{id}', name: 'supprimer_du_panier')]
    public function supprimer(int $id): Response
    {
        $user = $this->security->getUser();
        if (!$user) {
            $this->addFlash('warning', 'Vous devez être connecté pour modifier le panier.');
            return $this->redirectToRoute('app_login');
        }

        $cart = $this->cartRepository->findOneBy(['user' => $user]);
        if (!$cart) {
            $this->addFlash('danger', 'Panier introuvable.');
            return $this->redirectToRoute('panier_afficher');
        }

        foreach ($cart->getCartItems() as $item) {
            if ($item->getId() === $id) {
                $cart->removeCartItem($item);
                $this->em->remove($item);
                $this->em->flush();
                $this->addFlash('success', 'Produit supprimé du panier.');
                return $this->redirectToRoute('panier_afficher');
            }
        }

        $this->addFlash('danger', 'Produit non trouvé dans le panier.');
        return $this->redirectToRoute('panier_afficher');
    }

    #[Route('/vider', name: 'vider_panier')]
    public function vider(): Response
    {
        $user = $this->security->getUser();
        if (!$user) {
            $this->addFlash('warning', 'Vous devez être connecté pour modifier le panier.');
            return $this->redirectToRoute('app_login');
        }

        $cart = $this->cartRepository->findOneBy(['user' => $user]);
        if ($cart) {
            foreach ($cart->getCartItems() as $item) {
                $this->em->remove($item);
            }
            $this->em->flush();
            $this->addFlash('info', 'Le panier a été vidé.');
        }

        return $this->redirectToRoute('panier_afficher');
    }

    #[Route('/modifier-quantite/{id}', name: 'cart_update_quantity', methods: ['POST'])]
    public function modifierQuantite(int $id, Request $request): Response
    {
        $user = $this->security->getUser();
        if (!$user) {
            $this->addFlash('warning', 'Vous devez être connecté pour modifier le panier.');
            return $this->redirectToRoute('app_login');
        }

        $cart = $this->cartRepository->findOneBy(['user' => $user]);
        if (!$cart) {
            $this->addFlash('danger', 'Panier introuvable.');
            return $this->redirectToRoute('panier_afficher');
        }

        foreach ($cart->getCartItems() as $item) {
            if ($item->getId() === $id) {
                $action = $request->request->get('action');

                if ($action === 'increase') {
                    $item->setQuantity($item->getQuantity() + 1);
                } elseif ($action === 'decrease') {
                    $newQty = $item->getQuantity() - 1;
                    if ($newQty > 0) {
                        $item->setQuantity($newQty);
                    } else {
                        $cart->removeCartItem($item);
                        $this->em->remove($item);
                    }
                }

                $this->em->flush();
                break;
            }
        }

        return $this->redirectToRoute('panier_afficher');
    }
}
