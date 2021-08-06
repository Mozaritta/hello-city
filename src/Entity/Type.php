<?php

namespace App\Entity;

use App\Repository\TypeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TypeRepository::class)
 */
class Type
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $radio;

    /**
     * @ORM\Column(type="boolean")
     */
    private $checkbox;

    /**
     * @ORM\Column(type="boolean")
     */
    private $text;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRadio(): ?bool
    {
        return $this->radio;
    }

    public function setRadio(bool $radio): self
    {
        $this->radio = $radio;

        return $this;
    }

    public function getCheckbox(): ?bool
    {
        return $this->checkbox;
    }

    public function setCheckbox(bool $checkbox): self
    {
        $this->checkbox = $checkbox;

        return $this;
    }

    public function getText(): ?bool
    {
        return $this->text;
    }

    public function setText(bool $text): self
    {
        $this->text = $text;

        return $this;
    }
}
