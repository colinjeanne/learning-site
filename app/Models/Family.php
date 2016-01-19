<?php namespace App\Models;

use Doctrine\Common\Collections\ArrayCollection;

function createUserComparison(User $user)
{
    return function ($key, User $otherUser) use ($user) {
        return $otherUser->getId() === $user->getId();
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
    
    /**
     * @Id
     * @GeneratedValue
     * @Column(type="integer")
     * @var int
     */
    private $id;

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
        $this->members = new ArrayCollection();
        $this->invitations = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }
    
    public function getMembers()
    {
        return $this->members;
    }
    
    public function isFull()
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
            if ($this->isFull()) {
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
