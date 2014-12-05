<?php
namespace Amilio\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ChannelRepository extends EntityRepository
{

    public function findByNot($field, $value)
    {
        $qb = $this->createQueryBuilder('a');
        $qb->where($qb->expr()->not($qb->expr()->eq('a.'.$field, '?1')));
        $qb->setParameter(1, $value);
    
        return $qb->getQuery()
        ->getResult();
    }

}