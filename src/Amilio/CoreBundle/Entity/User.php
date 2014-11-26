<?php

namespace Amilio\CoreBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class User extends BaseUser
{
    /** 
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="Channel", inversedBy="users")
     * @ORM\JoinTable(name="users_channels")
     **/
    private $channels;  
    

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    public function addChannel(Channel $channel)
    {
        $this->channels[] = $channel;
        return $this;
    }
    
    public function getChannels()
    {
        return $this->channels;
    }
}