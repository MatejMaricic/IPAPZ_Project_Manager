<?php
/**
 * Created by PhpStorm.
 * User: matej
 * Date: 3/19/19
 * Time: 1:20 PM
 */

namespace App\Controller;


use App\Entity\Collaboration;
use App\Entity\User;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Braintree_Gateway;

class CollabController extends AbstractController
{

    /**
     * @Route("index/{id}/subscribe_to_collab", name="subscribe_to_collab")
     * @param EntityManagerInterface $entityManager
     * @param Collaboration $collaboration
     * @throws
     * @return Response
     */
    public function subscribeToCollab(EntityManagerInterface $entityManager, Collaboration $collaboration)
    {

        $token = $this->gateway();

//        $collaboration->setCreatedAt(new \DateTime('now'));
//        $collaboration->setSubscribedUntil(new \DateTime('now + 1 month'));
//        $collaboration->setSubscribed(true);
//
//        $entityManager->persist($collaboration);
//        $entityManager->flush();

        return $this->redirectToRoute('index_page');
    }

    /**
     * @Route("get_token", name ="get_token")
     * @return string
     */
    public function gateway()
    {
        $gateway = new Braintree_Gateway([
            'environment' => 'sandbox',
            'merchantId' => 'qmk79j9h7rxpjg8t',
            'publicKey' => 'pnz7bb5774j2j3n4',
            'privateKey' => '7c0e8443e507a26409dc23f6ca1afcb6'
        ]);

         return $gateway;
    }

    /**
     * @Route("checkout", name = "checkout")
     */
    public function checkout()
    {

        $amount = $_POST["amount"];
        $nonce = $_POST["payment_method_nonce"];
        $result = $this->gateway()->transaction()->sale([
            'amount' => $amount,
            'paymentMethodNonce' => $nonce,
            'options' => [
                'submitForSettlement' => true
            ]
        ]);
        if ($result->success || !is_null($result->transaction)) {
            $transaction = $result->transaction;
            header("Location: " . $baseUrl . "transaction.php?id=" . $transaction->id);
        } else {
            $errorString = "";
            foreach ($result->errors->deepAll() as $error) {
                $errorString .= 'Error: ' . $error->code . ": " . $error->message . "\n";
            }
            $_SESSION["errors"] = $errorString;
            header("Location: " . $baseUrl . "index.php");
        }
    }
}
