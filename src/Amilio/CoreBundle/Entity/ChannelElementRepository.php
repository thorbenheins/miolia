<?php
namespace Amilio\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ChannelElementRepository extends EntityRepository
{

    public function findChannels(Addable $element)
    {
        return $this->findBy(array("type" => get_class($element), "foreignId" => $element->getId()), array("id" => "desc"), 5);
    }

}