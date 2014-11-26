<?php
namespace Amilio\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ChannelRepository extends EntityRepository
{

    public function findOneByOwner(User $owner, $channelName)
    {
        return $this->findOneBy(array(
            "owner" => $owner,
            "name" => $channelName
        ));
        
        // $qb = $this->getEntityManager()->createQueryBuilder('channel');
        
        // $qb->from( 'AmilioCoreBundle:Channel a' )
        // ->leftJoin( 'A.AB ab' )
        // ->where( 'ab.id IS NULL' )
        // ->fetchArray()
        
        // $qb->select(array(
        // 'p'
        // ))
        // ->from('AmilioCoreBundle:Channel', 'p')
        // ->where('p.name = ' . $channelname)
        // ->join('p.users', 'c', 'WITH', $qb->expr()
        // ->in('c.id', $user->getId));
        // $result = $qb->getQuery()->execute();
        // return $result;
    }
}