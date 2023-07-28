<?php

namespace App\Entity;

use App\Repository\BorrowRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BorrowRepository::class)]
class Borrow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_borrow = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_return = null;

    #[ORM\ManyToOne(inversedBy: 'loans')]
    private ?Book $book = null;

    #[ORM\ManyToOne(inversedBy: 'loans')]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateBorrow(): ?\DateTimeInterface
    {
        return $this->date_borrow;
    }

    public function setDateBorrow(\DateTimeInterface $date_borrow): self
    {
        $this->date_borrow = $date_borrow;

        return $this;
    }

    public function getDateReturn(): ?\DateTimeInterface
    {
        return $this->date_return;
    }

    public function setDateReturn(?\DateTimeInterface $date_return): self
    {
        $this->date_return = $date_return;

        return $this;
    }

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): self
    {
        $this->book = $book;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
