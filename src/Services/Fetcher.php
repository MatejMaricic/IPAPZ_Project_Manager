<?php
/**
 * Created by PhpStorm.
 * User: matej
 * Date: 3/21/19
 * Time: 10:23 AM
 */

namespace App\Services;

use App\Entity\Collaboration;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Fetcher extends AbstractController
{
    public function checkSubscription()
    {
        if ($this->isGranted('ROLE_MANAGER')) {
            return $subscribed = $this->getUser()->getCollaboration()->getSubscribed();
        } else {
            return $subscribed = 1;
        }
    }

    public function subscriptionAmount(
        Collaboration $collaboration,
        UserRepository $userRepository
    ) {
        $user = $collaboration->getUser();
        $devs = $userRepository->findAllDevelopersForManagerArray($user->getId());
        $numOfDevs = count($devs);

        return $amount = ($numOfDevs * 5) + 5;
    }


    public function roleChecker(UserRepository $userRepository, $userParam, $adminParam, $route, $name)
    {

        if ($this->checkSubscription() == 0) {
            $gateway = $this->gateway()->ClientToken()->generate();

            return $this->render(
                'payment.html.twig',
                [
                    'user' => $this->getUser(),
                    'collab' => $this->getUser()->getCollaboration(),
                    'gateway' => $gateway,
                    'amount' => $this->subscriptionAmount($this->getUser()->getCollaboration(), $userRepository)
                ]
            );
        } elseif ($this->isGranted('ROLE_ADMIN')) {
            return $this->render(
                $route.'admin_'.$name.'.html.twig', $adminParam
            );
        } elseif ($this->isGranted('ROLE_USER')) {
            return $this->render(
                $route.'user_'.$name.'.html.twig', $userParam
            );
        }

        return $render = true;
    }

    private function gateway()
    {
        $gateway = new \Braintree_Gateway(
            [
                'environment' => 'sandbox',
                'merchantId' => 'qmk79j9h7rxpjg8t',
                'publicKey' => 'pnz7bb5774j2j3n4',
                'privateKey' => '7c0e8443e507a26409dc23f6ca1afcb6'
            ]
        );

        return $gateway;
    }
}
