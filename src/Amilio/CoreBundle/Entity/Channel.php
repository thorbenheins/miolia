<?php
namespace Amilio\CoreBundle\Entity;

use Symfony\Component\HttpFoundation\File\File;

use Amilio\CoreBundle\Util\Url;

use FOS\UserBundle\Util\Canonicalizer;

use Doctrine\ORM\Mapping as ORM;

/**
 * Channel
 * 
 * @todo add canonical name für url building * 
 *
 * @ORM\Table()
 * @ORM\Entity
 * 
 * @ORM\Entity(repositoryClass="Amilio\CoreBundle\Entity\ChannelRepository")
 */
class Channel
{
    const TYPE_CHANNEL = "0";
    const TYPE_COLLECTION = "1";

    /**
     *
     * @ORM\Column(name="id", type="integer")
     *      @ORM\Id
     *      @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @var string @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @ORM\Column(name="list_image", type="string", length=255, nullable=true)
     */
    private $listImage;

    /**
     * @ORM\Column(name="header_background_color", type="string", length=6, nullable=true)
     */
    private $headerBackgroundColor = "FFFFFF";

    /**
     *  @ORM\Column(name="type", type="string", length=1)
     */
    private $type;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="channels")
     */
    private $users;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="ownedChannels")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $owner;

    /** 
     * @ORM\Column(name="channel_of_the_week", type="boolean")
     */
    private $channel_of_the_week = false;
    
    /**
     * @ORM\Column(name="canonical_name", type="string", length=255)
     */
    private $canonical_name;
    
    /**
     * @ORM\OneToMany(targetEntity="ChannelElement", mappedBy="channel", cascade={"remove"})
     * @ORM\OrderBy({"id" = "DESC"})
     **/
    private $elements;

    /**
     *  @ORM\Column(name="isPremium", type="string", length=1)
     */
    private $isPremium = 0;

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
     * Set description
     *
     * @param string $description            
     * @return Channel
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set name
     *
     * @param string $name            
     * @return Channel
     */
    public function setName($name)
    {
        $this->name = $name;

        $this->canonical_name = Url::sluggify($name);

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get canonical name
     *
     * @return string
     */
    public function getCanonicalName()
    {
        return $this->canonical_name;
    }

    /**
     * Set image
     *
     * @param string $image            
     * @return Channel
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
        $image = new File(__DIR__ . "/../../../../web/" . $this->image);
        return $image;
    }

    /**
     * Set type
     *
     * @param string $type            
     * @return Channel
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
     * Set Owner
     *
     * @param string $type
     * @return Channel
     */
    public function setOwner(User $owner)
    {
        $this->owner = $owner;
        return $this;
    }

    /**
     * Get owner
     *
     * @return string
     */
    public function getOwner()
    {
        return $this->owner;
    }

    public function addElement(ChannelElement $element) 
    {
        $this->elements[] = $element;
    } 
       
    public function getElements()
    {
        return $this->elements;
    }

    public function getHeaderBackgroundColor()
    {
	return $this->headerBackgroundColor;
    }
}
