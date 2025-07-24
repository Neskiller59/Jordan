<?php

namespace App\Tools\Cart;

use App\Repository\ModelRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartTools
{
    protected $session;
    protected $productRepository;
    public $tva = 5.5;

    public function __construct(SessionInterface $session, ModelRepository $productRepository)
    {
        $this->session = $session;
        $this->productRepository = $productRepository;
    }

    public function add(int $id): void
    {
        $panier = $this->session->get('panier', []);

        if (!empty($panier[$id])) {
            $panier[$id]++;
        } else {
            $panier[$id] = 1;
        }

        $this->session->set('panier', $panier);
    }

    public function remove(int $id): void
    {
        $panier = $this->session->get('panier', []);

        if (!empty($panier[$id])) {
            unset($panier[$id]);
        }

        $this->session->set('panier', $panier);
    }

    public function getFullCart(): array
    {
        $panier = $this->session->get('panier', []);
        $panierWithData = [];

        foreach ($panier as $id => $quantity) {
            $product = $this->productRepository->find($id);
            if ($product) {
                $panierWithData[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                ];
            }
        }

        return $panierWithData;
    }

    public function getTotalTTC(): float
    {
        $total = 0;

        foreach ($this->getFullCart() as $item) {
            $total += $item['product']->getPrix() * $item['quantity'];
        }

        return $total;
    }

    public function getTotalItem(): int
    {
        $total = 0;

        foreach ($this->getFullCart() as $item) {
            $total += $item['quantity'];
        }

        return $total;
    }

    public function getTva(): float
    {
        return $this->tva;
    }

    public function getTotalTVA(): float
    {
        return $this->getTotalTTC() * $this->getTva() / 100;
    }

    public function getSession(): SessionInterface
    {
        return $this->session;
    }
}
