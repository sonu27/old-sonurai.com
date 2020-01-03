<?php

namespace App\Repository;

use App\Entity\BingWallpaper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BingWallpaperRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
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

    /**
     * @param int $offset
     * @param int $limit
     *
     * @return BingWallpaper[]
     */
    public function get(int $offset = 0, int $limit = 10): array
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
            ->andWhere('w.market = :market')
            ->setParameter('name', $name.'%')
            ->setParameter('market', $market)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $nameId
     * @return BingWallpaper|null
     */
    public function findOneByNameId(string $nameId)
    {
        return $this->findOneBy(['nameId' => $nameId]);
    }

    /**
     * @return BingWallpaper[]
     */
    public function findByEmptyData(): array
    {
        $wallpapers = [];

        $rows =  $this->_em->getConnection()->fetchAll(
            'SELECT id FROM bing_wallpaper WHERE data = JSON_ARRAY()'
        );

        foreach ($rows as $row) {
            $wallpapers[] = $this->find((int) $row['id']);
        }

        return $wallpapers;
    }

    public function save(BingWallpaper $wallpaper)
    {
        $this->_em->persist($wallpaper);
        $this->_em->flush();
    }
}
