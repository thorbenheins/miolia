<?php
namespace Amilio\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ChannelRepository extends EntityRepository
{

    public function findChannelsOfTheWeek()
    {
        return $this->findBy(array("channel_of_the_week" => true), array("id" => "desc"), 5);
    }

}