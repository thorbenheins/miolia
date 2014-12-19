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

    public function findFavouritesByUser(User $user)
    {
	$channels = $user->getFavouriteChannels();

	$channelString = "0";

	foreach( $channels as $channel) {
		$channelString .= ', ' . $channel->getId();
	}

	$qb = $this->createQueryBuilder('ce');
	$qb->where($qb->expr()->in('ce.channel', $channelString));
	$qb->orderBy('ce.id', 'DESC');
	return $qb->getQuery()->getResult();
    }

    public function findByProduct(Product $product)
    {
	$qb = $this->createQueryBuilder('ce');
        #$qb->select('DISTINCT ce.channel_id');
	$qb->where('ce.foreignId = ' . $product->getId());
	#$qb->where('ce.type = "Amilio\\CoreBundle\\Entity\\Product"');
        return $qb->getQuery()->getResult();
    }
}
