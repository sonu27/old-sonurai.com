<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BingWallpaper
 *
 * @ORM\Table(name="bing_wallpaper",
 *            indexes={@ORM\Index(name="index_all", columns={"date", "name", "description", "market"})})
 * @ORM\Entity(repositoryClass="AppBundle\Entity\BingWallpaperRepository")
 */
class BingWallpaper implements \JsonSerializable
{
    /**
     * @var integer
     *
     * @ORM\Column(name="date", type="integer", nullable=false)
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=false)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="market", type="string", length=10, nullable=false)
     */
    private $market;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;


    public function getId(): int
    {
        return $this->id;
    }

    public function setDate(int $date): BingWallpaper
    {
        $this->date = $date;

        return $this;
    }

    public function getDate(): int
    {
        return $this->date;
    }

    public function setName(string $name): BingWallpaper
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setDescription(string $description): BingWallpaper
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setMarket(string $market): BingWallpaper
    {
        $this->market = $market;

        return $this;
    }

    public function getMarket(): string
    {
        return $this->market;
    }

    public function jsonSerialize(): array
    {
        $copyright = trim(str_replace(')', '', explode('(', $this->description)[1]));
        $title     = trim(explode('(', $this->description)[0]);

        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'desc'      => $title,
            'copyright' => $copyright,
            'date'      => $this->date,
            'market'    => $this->market,
        ];
    }
}
