<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class BingMarketRepository extends EntityRepository
{
    public function findAll()
    {
        return $this->findBy(array());
    }

    public function save(BingMarket $market)
    {
        $this->_em->persist($market);
        $this->_em->flush();
    }
}
