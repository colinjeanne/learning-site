<?php namespace App\Models;

/**
 * @Entity
 * @Table(name="ActivityLinks")
 */
class ActivityLink
{
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
    private $title;
    
    /**
     * @Column(type="string", length=2048, nullable=false)
     * @var string
     */
    private $uri;
    
    /**
     * @ManyToOne(targetEntity="App\Models\Activity", inversedBy="links")
     * @JoinColumn(name="activity_id", referencedColumnName="id")
     * @var App\Models\Activity
     */
    private $activity;

    public function __construct($title, $uri)
    {
        $this->setTitle($title);
        $this->setUri($uri);
    }

    public function getId()
    {
        return $this->id;
    }
    
    public function getTitle()
    {
        return $this->title;
    }
    
    public function setTitle($title)
    {
        if (!is_string($title) || (strlen($title) > 64)) {
            throw new \InvalidArgumentException('Invalid link title');
        }
        
        $this->title = $title;
    }
    
    public function getURI()
    {
        return $this->uri;
    }
    
    public function setURI($uri)
    {
        if (!is_string($uri) || (strlen($uri) > 2048)) {
            throw new \InvalidArgumentException('Invalid link URI');
        }
        
        $this->uri = $uri;
    }
    
    public function getActivity()
    {
        return $this->activity;
    }
    
    public function setActivity(Activity $activity = null)
    {
        return $this->activity = $activity;
    }
    
    public function equals(ActivityLink $activityLink)
    {
        return ($this->getTitle() === $activityLink->getTitle()) &&
            ($this->getURI() === $activityLink->getURI());
    }
}
