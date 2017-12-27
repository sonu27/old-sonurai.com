<?php

namespace App\Repository;

use App\Entity\BingWallpaper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class BingWallpaperRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BingWallpaper::class);
    }

    public function countAll()
    {
        return $this->createQueryBuilder('w')
            ->select('count(w.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function get($offset = 0, $limit = 10)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->select('w')
            ->from(BingWallpaper::class, 'w')
            ->orderBy('w.date', 'DESC')
            ->addOrderBy('w.id', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function findByMarket($market)
    {
        return $this->findBy(['market' => $market]);
    }

    public function countSearch($query)
    {
        $result = $this->createQueryBuilder('w')
            ->select('count(w.id)')
            ->where('w.description LIKE :description')
            ->setParameter('description', '%'.$query.'%')
            ->getQuery()
            ->getSingleScalarResult();

        return $result;
    }

    public function search($query, $offset = 0, $limit = 10)
    {
        $result = $this->createQueryBuilder('w')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->where('w.description LIKE :description')
            ->orderBy('w.date', 'DESC')
            ->setParameter('description', '%'.$query.'%')
            ->getQuery()
            ->getResult();

        return $result;
    }

    public function findOneLikeName($name)
    {
        $result = $this->createQueryBuilder('w')
            ->where('w.name LIKE :name')
            ->setParameter('name', $name.'%')
            ->getQuery()
            ->getResult();

        if (!empty($result)) {
            return $result[0];
        }
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
