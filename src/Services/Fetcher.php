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
}
