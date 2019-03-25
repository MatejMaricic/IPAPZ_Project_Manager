<?php

namespace App\Entity;

/**
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="App\Repository\TransactionsRepository")
 */
class Transactions
{
    /**
     * @Doctrine\ORM\Mapping\Id()
     * @Doctrine\ORM\Mapping\GeneratedValue()
     * @Doctrine\ORM\Mapping\Column(type="integer")
     */
    private $id;

    /**
     * @Doctrine\ORM\Mapping\Column(type="string")
     */
    private $transactionId;

    /**
     * @Doctrine\ORM\Mapping\Column(type="string", length=255)
     */
    private $currency;

    /**
     * @Doctrine\ORM\Mapping\Column(type="integer")
     */
    private $amount;

    /**
     * @Doctrine\ORM\Mapping\Column(type="string", length=255)
     */
    private $buyerEmail;

    /**
     * @Doctrine\ORM\Mapping\Column(type="datetime")
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
