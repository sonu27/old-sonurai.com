<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class BingWallpaperRepository extends EntityRepository
{
    public function countAll()
    {
        return $this->createQueryBuilder('w')
            ->select('count(w.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function get($offset = 0, $limit = 10)
    {
        return $this->createQueryBuilder('w')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->orderBy('w.date', 'DESC')
            ->addOrderBy('w.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByMarket($market)
    {
        return $this->findBy(['market' => $market]);
    }

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
