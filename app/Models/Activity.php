<?php namespace App\Models;

use Doctrine\Common\Collections\ArrayCollection;

function createLinkComparison(ActivityLink $link)
{
    return function ($key, ActivityLink $otherLink) use ($link) {
        return $otherLink->equals($link);
    };
}

/**
 * @Entity
 * @Table(name="Activities")
 */
class Activity
{
    const MAX_NUMBER_OF_LINKS = 10;
    
    /**
     * @Id
     * @GeneratedValue
     * @Column(type="integer")
     * @var int
     */
    private $id;
    
    /**
     * @Column(type="string", length=64, nullable=false)
     * @var string
     */
    private $name;
    
    /**
     * @Column(type="string", length=4096, nullable=false)
     * @var string
     */
    private $description;
    
    /**
     * @ManyToOne(targetEntity="App\Models\Family", inversedBy="activities")
     * @JoinColumn(name="family_id", referencedColumnName="id")
     * @var App\Models\Family
     */
    private $family;
    
    /**
     * @OneToMany(targetEntity="App\Models\ActivityLink", mappedBy="activity")
     * @var App\Models\ActivityLink[]
     */
    private $links;

    public function __construct($name)
    {
        $this->setName($name);
        $this->setDescription('');
        $this->links = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }
    
    public function getFamily()
    {
        return $this->family;
    }
    
    public function setFamily(Family $family)
    {
        $this->family = $family;
        $family->addActivity($this);
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setName($name)
    {
        if (!is_string($name) || (strlen($name) > 64)) {
            throw new \InvalidArgumentException('Invalid activity name');
        }
        
        $this->name = $name;
    }
    
    public function getDescription()
    {
        return $this->description;
    }
    
    public function setDescription($description)
    {
        if (!is_string($description) || (strlen($description) > 4096)) {
            throw new \InvalidArgumentException(
                'Invalid activity description'
            );
        }
        
        $this->description = $description;
    }
    
    public function getLinks()
    {
        return $this->links ? $this->links : new ArrayCollection();
    }
    
    public function hasMaxLinks()
    {
        return $this->getLinks()->count() == self::MAX_NUMBER_OF_LINKS;
    }
    
    public function hasLink(ActivityLink $link)
    {
        $comparer = createLinkComparison($link);
        return $this->getLinks()->exists($comparer);
    }

    public function addLink(ActivityLink $link)
    {
        if (!$this->hasLink($link)) {
            if ($this->hasMaxLinks()) {
                throw new \InvalidArgumentException('Too many links');
            }
            
            $this->getLinks()[] = $link;
        }
    }
    
    public function removeLinkAtIndex($index)
    {
        $activityLink = $this->getLinks()->remove($index);
        if ($activityLink) {
            $activityLink->setActivity(null);
        }
    }
}
