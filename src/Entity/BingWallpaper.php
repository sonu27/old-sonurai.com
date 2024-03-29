<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BingWallpaper
 *
 * @ORM\Table(
 *     name="bing_wallpaper",
 *     indexes={
 *         @ORM\Index(name="name_id", columns={"name_id"}),
 *         @ORM\Index(name="name", columns={"name"}),
 *         @ORM\Index(name="description", columns={"description"}),
 *         @ORM\Index(name="market", columns={"market"}),
 * })
 * @ORM\Entity(repositoryClass="App\Repository\BingWallpaperRepository")
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
     * @var array
     *
     * @ORM\Column(name="data", type="json", nullable=false)
     */
    private $data = [];

    /**
     * @var string
     *
     * @ORM\Column(name="name_id", type="string", length=255, nullable=true)
     */
    private $nameId;

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

        $this->nameId = trim(explode('_', $name)[0]);

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getNameId(): ?string
    {
        return $this->nameId;
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

    public function setData(array $data): BingWallpaper
    {
        $this->data = $data;

        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getTags(): array
    {
        $tags = [];

        if (empty($this->data)) {
            return $tags;
        }

        $data = $this->data[0];

        if (isset($data['labelAnnotations'])) {
            foreach ($data['labelAnnotations'] as $label) {
                $tags[] = $label['description'];
            }
        }

        return $tags;
    }

    public function getColours(): array
    {
        $colours = [];

        if (empty($this->data)) {
            return $colours;
        }

        $data = $this->data[0];

        if (isset($data['imagePropertiesAnnotation']['dominantColors'])) {
            $cs = $data['imagePropertiesAnnotation']['dominantColors']['colors'];
            foreach ($cs as $colour) {
                $c         = $colour['color'];
                $colours[] = sprintf('#%02x%02x%02x', $c['red'], $c['green'], $c['blue']);
            }
        }

        return $colours;
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
