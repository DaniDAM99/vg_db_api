<?php

namespace App\Entity;

use App\Repository\JuegoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=JuegoRepository::class)
 */
class Juego
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fecha_lanzamiento;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $genero;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $plataforma;

    /**
     * @ORM\ManyToMany(targetEntity=Lista::class, mappedBy="juegos")
     */
    private $listas;

    public function __construct()
    {
        $this->listas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getFechaLanzamiento(): ?\DateTimeInterface
    {
        return $this->fecha_lanzamiento;
    }

    public function setFechaLanzamiento(?\DateTimeInterface $fecha_lanzamiento): self
    {
        $this->fecha_lanzamiento = $fecha_lanzamiento;

        return $this;
    }

    public function getGenero(): ?string
    {
        return $this->genero;
    }

    public function setGenero(?string $genero): self
    {
        $this->genero = $genero;

        return $this;
    }

    public function getPlataforma(): ?string
    {
        return $this->plataforma;
    }

    public function setPlataforma(?string $plataforma): self
    {
        $this->plataforma = $plataforma;

        return $this;
    }

    /**
     * @return Collection|Lista[]
     */
    public function getListas(): Collection
    {
        return $this->listas;
    }

    public function addLista(Lista $lista): self
    {
        if (!$this->listas->contains($lista)) {
            $this->listas[] = $lista;
            $lista->addJuego($this);
        }

        return $this;
    }

    public function removeLista(Lista $lista): self
    {
        if ($this->listas->removeElement($lista)) {
            $lista->removeJuego($this);
        }

        return $this;
    }
}
