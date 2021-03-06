<?php
/**
 * Created by PhpStorm.
 * User: matej
 * Date: 3/19/19
 * Time: 1:20 PM
 */

namespace App\Controller;

use App\Entity\Collaboration;
use App\Entity\Transactions;
use App\Repository\CollaborationRepository;
use App\Repository\UserRepository;
use App\Services\Fetcher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Braintree_Gateway;

class CollabController extends AbstractController
{


    private function gateway()
    {
        $gateway = new Braintree_Gateway(
            [
                'environment' => getenv('BT_ENVIRONMENT'),
                'merchantId' => getenv('BT_MERCHANT_ID'),
                'publicKey' => getenv('BT_PUBLIC_KEY'),
                'privateKey' => getenv('BT_PRIVATE_KEY')
            ]
        );

        return $gateway;
    }


    /**
     * @Symfony\Component\Routing\Annotation\Route("/manager/checkout/{id}", name = "checkout")
     * @param                  Collaboration $collaboration
     * @param                  EntityManagerInterface $entityManager
     * @param                  UserRepository $userRepository
     * @param                  Fetcher $fetcher
     * @param                  Request $request
     * @throws
     * @return                 \Symfony\Component\HttpFoundation\Response
     */
    public function checkout(
        Collaboration $collaboration,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        Fetcher $fetcher,
        Request $request
    ) {

        $amount = $fetcher->subscriptionAmount($collaboration, $userRepository);
        $nonce = $request->get("payment_method_nonce");
        $result = $this->gateway()->transaction()->sale(
            [
                'amount' => $amount,
                'paymentMethodNonce' => $nonce,
                'options' => [
                    'submitForSettlement' => true
                ]
            ]
        );
        if ($result->success || !is_null($result->transaction)) {
            $transaction = $result->transaction;

            $collaboration->setSubscribed(true);
            $collaboration->setSubscribedUntil(new \DateTime('now + 1 month'));
            $transactions = new Transactions();
            $transactions->setAmount($amount);
            $transactions->setTransactionId($transaction->id);
            $transactions->setBoughtAt(new \DateTime('now'));
            $transactions->setBuyerEmail($collaboration->getUser()->getEmail());
            $transactions->setCurrency($transaction->currencyIsoCode);

            $entityManager->persist($transactions);
            $entityManager->persist($collaboration);
            $entityManager->flush();

            return $this->redirectToRoute('index_page');
        } else {
            $errorString = "";
            foreach ($result->errors->deepAll() as $error) {
                $errorString .= 'Error: ' . $error->code . ": " . $error->message . "\n";
            }

            return $this->redirectToRoute('index_page');
        }
    }


    /**
     * @param  CollaborationRepository $collaborationRepository
     * @param  EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws
     */
    public function checkSubscriptionDate(
        CollaborationRepository $collaborationRepository,
        EntityManagerInterface $entityManager
    ) {
        $subscriptions = $collaborationRepository->findAll();
        $today = new \DateTime('now');

        foreach ($subscriptions as $subscription) {
            if ($subscription->getSubscribedUntil() < $today) {
                $subscription->setSubscribed(false);

                $entityManager->persist($subscription);
                $entityManager->flush();
            }
        }

        return $this->redirectToRoute('index_page');
    }
}
