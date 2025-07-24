<?php
namespace App\EventSubscriber;

use App\Repository\CartRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class CartSubscriber implements EventSubscriberInterface
{
    private Security $security;
    private CartRepository $cartRepository;
    private Environment $twig;

    public function __construct(Security $security, CartRepository $cartRepository, Environment $twig)
    {
        $this->security = $security;
        $this->cartRepository = $cartRepository;
        $this->twig = $twig;
    }

    public function onKernelController(ControllerEvent $event)
    {
        $user = $this->security->getUser();

        $cart = null;
        if ($user) {
            $cart = $this->cartRepository->findOneBy(['user' => $user]);
        }

        // injecte dans toutes les vues twig la variable 'cart'
        $this->twig->addGlobal('cart', $cart);
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
