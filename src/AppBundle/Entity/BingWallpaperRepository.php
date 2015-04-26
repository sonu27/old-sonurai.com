<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class BingWallpaperRepository extends EntityRepository
{
    public function findByNameAndMarket($name, $market)
    {
        return $this->createQueryBuilder('w')
            ->where('w.name LIKE :name')
            ->andWhere("w.market = :market")
            ->setParameter('name', $name.'%')
            ->setParameter('market', $market)
            ->getQuery()
            ->getResult();
    }

    public function save(BingWallpaper $wallpaper)
    {
        $this->_em->persist($wallpaper);
        $this->_em->flush();
    }
}
