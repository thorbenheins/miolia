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
	$qb = $this->createQueryBuilder('c');
        $qb->where('c.parent is NULL');
        $qb->setMaxResults($count);
        $qb->orderBy('c.followerCount', 'DESC');
        return $qb->getQuery()->getResult();
    }

    public function findByProduct(Product $product)
    {
        $qb = $this->createQueryBuilder('ch');

	$qb->join('ch.elements', 'ce');
	$qb->where($qb->expr()->andX('ce.foreignId = ' . $product->getId(), 'ce.type = :type'));
        $qb->setParameter('type', get_class($product));
        return $qb->getQuery()->getResult();
    }
}
