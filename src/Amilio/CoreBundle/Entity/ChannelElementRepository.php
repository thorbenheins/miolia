<?php
namespace Amilio\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Amilio\CoreBundle\Entity\User;
use Amilio\CoreBundle\Entity\Product;

class ChannelElementRepository extends EntityRepository
{
    public function findChannels(Addable $element)
    {
	
        return $this->findBy(array("type" => get_class($element), "foreignId" => $element->getId()), array("id" => "desc"), 5);
    }

    public function findFavouritesByUser(User $user, $start, $count)
    {
	$qb = $this->createQueryBuilder('ce');
	$qb->join('ce.channel', 'c');
	$qb->join('c.owner', 'o');
	$qb->join('o.favouriteChannels', 'fc');
	$qb->where('c.owner = :user'); 
	$qb->orderBy('ce.id', 'DESC');
	$qb->distinct();
	$qb->setMaxResults($count);
	$qb->setFirstResult($start);
	$qb->setParameter('user', $user);
	return $qb->getQuery()->getResult();
    }

    public function findByProduct(Product $product)
    {
	return array();

	$qb = $this->createQueryBuilder('ce');
	
	$qb->select('ch');
	$qb->where('ce.foreignId = ' . $product->getId());
	$qb->where('ce.type = :type');
	$qb->join('ce.channel', 'ch');

	
	
	$qb->setParameter('type', get_class($product));
        return $qb->getQuery()->getResult();
    }
}
