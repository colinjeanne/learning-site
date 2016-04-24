<?php namespace App\Models;

use Doctrine\Common\Collections\ArrayCollection;

function createActivityComparison(Activity $activity)
{
    return function ($key, Activity $otherActivity) use ($activity) {
        return $otherActivity->getId() === $activity->getId();
    };
}

function createUserComparison(User $user)
{
    return function ($key, User $otherUser) use ($user) {
        return $otherUser->getId() === $user->getId();
    };
}

function createChildComparison(Child $child)
{
    return function ($key, Child $otherChild) use ($child) {
        return $otherChild->getId() === $child->getId();
    };
}

/**
 * @Entity
 * @Table(name="Families")
 */
class Family
{
    const MAX_NUMBER_OF_MEMBERS = 10;
    const MAX_NUMBER_OF_INVITATIONS = 10;
    const MAX_NUMBER_OF_CHILDREN = 10;
    const MAX_NUMBER_OF_ACTIVITIES = 255;
    
    /**
     * @Id
     * @GeneratedValue
     * @Column(type="integer")
     * @var int
     */
    private $id;
    
    /**
     * @OneToMany(targetEntity="App\Models\Activity", mappedBy="family")
     * @var App\Models\Activity[]
     */
    private $activities;
    
    /**
     * @OneToMany(targetEntity="App\Models\Child", mappedBy="family")
     * @var App\Models\Child[]
     */
    private $children;

    /**
     * @OneToMany(targetEntity="App\Models\User", mappedBy="family")
     * @var App\Models\User[]
     */
    private $members;
    
    /**
     * @OneToMany(targetEntity="App\Models\FamilyInvitation", mappedBy="family")
     * @var App\Models\FamilyInvitations[]
     */
    private $invitations;

    public function __construct()
    {
        $this->activities = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->members = new ArrayCollection();
        $this->invitations = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }
    
    public function getActivities()
    {
        return $this->activities ? $this->activities : new ArrayCollection();
    }
    
    public function hasMaxActivities()
    {
        return $this->getActivities()->count() ==
            self::MAX_NUMBER_OF_ACTIVITIES;
    }
    
    public function hasActivity(Activity $activity)
    {
        $comparer = createActivityComparison($activity);
        return $this->getActivities()->exists($comparer);
    }

    public function addActivity(Activity $activity)
    {
        if (!$this->hasActivity($activity)) {
            if ($this->hasMaxActivities()) {
                throw new \InvalidArgumentException('Too many activities');
            }
            
            $this->getActivities()[] = $activity;
            $activity->setFamily($this);
        }
    }
    
    public function getChildren()
    {
        return $this->children ? $this->children : new ArrayCollection();
    }
    
    public function hasMaxChildren()
    {
        return $this->getChildren()->count() == self::MAX_NUMBER_OF_CHILDREN;
    }
    
    public function hasChild(Child $child)
    {
        $comparer = createChildComparison($child);
        return $this->getChildren()->exists($comparer);
    }

    public function addChild(Child $child)
    {
        if (!$this->hasChild($child)) {
            if ($this->hasMaxChildren()) {
                throw new \InvalidArgumentException('Too many children');
            }
            
            $this->getChildren()[] = $child;
            $child->setFamily($this);
        }
    }
    
    public function getMembers()
    {
        return $this->members ? $this->members : new ArrayCollection();
    }
    
    public function hasMaxMembers()
    {
        return $this->getMembers()->count() == self::MAX_NUMBER_OF_MEMBERS;
    }
    
    public function hasMember(User $user)
    {
        $comparer = createUserComparison($user);
        return $this->getMembers()->exists($comparer);
    }

    public function addMember(User $user)
    {
        if (!$this->hasMember($user)) {
            if ($this->hasMaxMembers()) {
                throw new \InvalidArgumentException('Too many members');
            }
            
            $this->getMembers()[] = $user;
            $user->setFamily($this);
        }
    }
    
    public function isEmpty()
    {
        $memberCount = $this->getMembers()->count();
        $childrenCount = $this->getChildren()->count();
        
        return ($memberCount === 1) && ($childrenCount === 0);
    }
    
    public function getInvitations()
    {
        return $this->invitations;
    }
    
    public function addInvitation(FamilyInvitation $invitation)
    {
        $this->invitations[] = $invitation;
    }
    
    public function canInviteMembers()
    {
        return $this->invitations->count() < self::MAX_NUMBER_OF_INVITATIONS;
    }
}
