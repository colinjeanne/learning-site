<?php namespace App\Models;

use Doctrine\Common\Collections\ArrayCollection;

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
    
    /**
     * @Id
     * @GeneratedValue
     * @Column(type="integer")
     * @var int
     */
    private $id;
    
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
        $this->children = new ArrayCollection();
        $this->members = new ArrayCollection();
        $this->invitations = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }
    
    public function getChildren()
    {
        return $this->children;
    }
    
    public function hasMaxChildren()
    {
        return $this->children->count() == self::MAX_NUMBER_OF_CHILDREN;
    }
    
    public function hasChild(Child $child)
    {
        $comparer = createChildComparison($child);
        return $this->children->exists($comparer);
    }

    public function addChild(Child $child)
    {
        if (!$this->hasChild($child)) {
            if ($this->hasMaxChildren()) {
                throw new \InvalidArgumentException('Too many children');
            }
            
            $this->children[] = $child;
            $child->setFamily($this);
        }
    }
    
    public function getMembers()
    {
        return $this->members;
    }
    
    public function hasMaxMembers()
    {
        return $this->members->count() == self::MAX_NUMBER_OF_MEMBERS;
    }
    
    public function hasMember(User $user)
    {
        $comparer = createUserComparison($user);
        return $this->members->exists($comparer);
    }

    public function addMember(User $user)
    {
        if (!$this->hasMember($user)) {
            if ($this->hasMaxMembers()) {
                throw new \InvalidArgumentException('Too many members');
            }
            
            $this->members[] = $user;
            $user->setFamily($this);
        }
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
