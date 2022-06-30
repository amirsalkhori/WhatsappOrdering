<?php


namespace App\Bridge;


interface UpdatedAt
{
    public function getUpdatedAt(): ?\DateTimeInterface;
    public function setUpdatedAt(\DateTimeInterface $createdAt): self;
}
