<?php
namespace Amilio\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Channel
 * 
 * @todo add canonical name für url building * 
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Channel
{

    const TYPE_STANDARD = "u";

    const TYPE_PREMIUM = "p";

    /**
     *
     * @var integer @ORM\Column(name="id", type="integer")
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
     * @var string @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     *
     * @var string @ORM\Column(name="image", type="string", length=255)
     */
    private $image;

    /**
     *  
     * @var string @ORM\Column(name="type", type="string", length=1)
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
     * @ORM\ManyToMany(targetEntity="Product", mappedBy="channels")
     */
    private $products;

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

    public function addProduct(Product $product)
    {
        $this->products[] = $product;
        return $this;
    }

    public function getProducts()
    {
        return $this->products;
    }
}
