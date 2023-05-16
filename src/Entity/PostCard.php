<?php

namespace App\Entity;

use App\Repository\PostCardRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostCardRepository::class)]
class PostCard
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $post_id = null;

    #[ORM\Column(length: 255)]
    private ?string $card_title = null;

    #[ORM\Column(length: 255)]
    private ?string $card_image = null;

    #[ORM\Column(length: 255)]
    private ?string $paragraph = null;

    #[ORM\Column(length: 255)]
    private ?string $template = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sorting_new = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPostId(): ?int
    {
        return $this->post_id;
    }

    public function setPostId(int $post_id): self
    {
        $this->post_id = $post_id;

        return $this;
    }

    public function getCardTitle(): ?string
    {
        return $this->card_title;
    }

    public function setCardTitle(string $card_title): self
    {
        $this->card_title = $card_title;

        return $this;
    }

    public function getCardImage(): ?string
    {
        return $this->card_image;
    }

    public function setCardImage(string $card_image): self
    {
        $this->card_image = $card_image;

        return $this;
    }

    public function getParagraph(): ?string
    {
        return $this->paragraph;
    }

    public function setParagraph(string $paragraph): self
    {
        $this->paragraph = $paragraph;

        return $this;
    }

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function setTemplate(string $template): self
    {
        $this->template = $template;

        return $this;
    }

    public function getSortingNew(): ?string
    {
        return $this->sorting_new;
    }

    public function setSortingNew(?string $sorting_new): self
    {
        $this->sorting_new = $sorting_new;

        return $this;
    }
}
