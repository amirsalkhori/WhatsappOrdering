<?php


namespace App\Bridge;


interface CreatedAt
{
    public function getCreatedAt(): ?\DateTimeInterface;
    public function setCreatedAt(\DateTimeInterface $createdAt): self;
}