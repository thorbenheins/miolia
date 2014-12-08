<?php

namespace Amilio\CoreBundle\Entity;

use Doctrine\Bundle\DoctrineBundle\Registry;

use Doctrine\ORM\Mapping as ORM;

/**
 * ChannelElement
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ChannelElement
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=100)
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="foreign_id", type="integer")
     */
    private $foreignId;


    /**
     * @ORM\ManyToOne(targetEntity="Channel", inversedBy="elements")
     * @ORM\JoinColumn(name="channel_id", referencedColumnName="id")
     **/
    private $channel;
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return ChannelElement
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set foreignId
     *
     * @param integer $foreignId
     * @return ChannelElement
     */
    public function setForeignId($foreignId)
    {
        $this->foreignId = $foreignId;

        return $this;
    }

    /**
     * Get foreignId
     *
     * @return integer 
     */
    public function getForeignId()
    {
        return $this->foreignId;
    }

    
    public function setElement(Addable $element)
    {
        $this->type = get_class($element);
        var_dump($element->getId());
        $this->setForeignId($element->getId());
    }
    
    public function getElement()
    {
        $em = Registry::getEntityManager();
        return $em->getRepository($this->type)->findOneBy(array('id' => $this->getForeignId()));
    }
    
    /**
     * @return the $channel
     */
    public function getChannel()
    {
        return $this->channel;
    }

	/**
     * @param field_type $channel
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;
    }
}
