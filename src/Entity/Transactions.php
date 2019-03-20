<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TransactionsRepository")
 */
class Transactions
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $transactionId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $currency;

    /**
     * @ORM\Column(type="integer")
     */
    private $amount;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $buyerEmail;

    /**
     * @ORM\Column(type="datetime")
     */
    private $boughtAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTransactionId()
    {
        return $this->transactionId;
    }

    public function setTransactionId($transactionId): self
    {
        $this->transactionId = $transactionId;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getBuyerEmail(): ?string
    {
        return $this->buyerEmail;
    }

    public function setBuyerEmail(string $buyerEmail): self
    {
        $this->buyerEmail = $buyerEmail;

        return $this;
    }

    public function getBoughtAt(): ?\DateTimeInterface
    {
        return $this->boughtAt;
    }

    public function setBoughtAt(\DateTimeInterface $boughtAt): self
    {
        $this->boughtAt = $boughtAt;

        return $this;
    }
}
