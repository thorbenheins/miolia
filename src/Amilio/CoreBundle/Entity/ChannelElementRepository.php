<?php
namespace Amilio\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Amilio\CoreBundle\Entity\User;

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
	return $qb->getQuery()->getResult();
    }
}
