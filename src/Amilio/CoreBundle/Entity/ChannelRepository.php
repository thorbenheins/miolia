<?php
namespace Amilio\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ChannelRepository extends EntityRepository
{
    public function findChannelsOfTheWeek()
    {
        return $this->findBy(array("channel_of_the_week" => true), array("id" => "desc"), 5);
    }

    public function findNewest($count = 5)
    {
	$qb = $this->createQueryBuilder('c');
        $qb->where('c.parent is NULL');
        $qb->setMaxResults($count);
	$qb->orderBy('c.id', 'DESC');
	return $qb->getQuery()->getResult();
    }

    public function findMostFollowed($count = 5)
    {
        return $this->findBy(array(), array("followerCount" => "desc"), $count);
    }
}
