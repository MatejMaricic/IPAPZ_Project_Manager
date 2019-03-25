<?php
/**
 * Created by PhpStorm.
 * User: matej
 * Date: 3/21/19
 * Time: 1:33 PM
 */

namespace App\Controller;

use App\Repository\TransactionsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    /**
     * @Symfony\Component\Routing\Annotation\Route("/invoice", name="invoice")
     * @param             TransactionsRepository $transactionsRepository
     * @return            \Symfony\Component\HttpFoundation\Response
     */
    public function showInvoice(TransactionsRepository $transactionsRepository)
    {
        $transactions = $transactionsRepository->findAll();

        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->render(
                'invoice.html.twig',
                [
                'user' => $this->getUser(),
                'transactions' => $transactions
                ]
            );
        }
    }
}
