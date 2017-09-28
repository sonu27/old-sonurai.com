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

    /**
     * Set date
     *
     * @param  integer       $date
     * @return BingWallpaper
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return integer
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set name
     *
     * @param  string        $name
     * @return BingWallpaper
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param  string        $description
     * @return BingWallpaper
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set market
     *
     * @param  string        $market
     * @return BingWallpaper
     */
    public function setMarket($market)
    {
        $this->market = $market;

        return $this;
    }

    /**
     * Get market
     *
     * @return string
     */
    public function getMarket()
    {
        return $this->market;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function jsonSerialize()
    {
        return [
            'id'     => $this->id,
            'name'   => $this->name,
            'desc'   => $this->description,
            'date'   => $this->date,
            'market' => $this->market,
        ];
    }
}
