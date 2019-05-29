<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LocationRepository")
 * @ORM\Table(name="location", indexes={
 *     @ORM\Index(name="idx_latitude", columns={"latitude"}),
 *     @ORM\Index(name="idx_longitude", columns={"longitude"}),
 * })
 */
class Location
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="decimal", precision=14, scale=12)
     */
    private $latitude;
    /**
     * @ORM\Column(type="decimal", precision=14, scale=12)
     */
    private $longitude;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param $latitude
     * @return Location
     */
    public function setLatitude($latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param $longitude
     * @return Location
     */
    public function setLongitude($longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }
}
