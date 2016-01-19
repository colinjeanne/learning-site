<?php namespace App\Models;

/**
 * @Entity
 * @Table(
 *  name="FamilyInvitations",
 *  uniqueConstraints={
 *      @UniqueConstraint(name="user_family_indx", columns={"user_id", "family_id"})
 *  }
 * )
 */
class FamilyInvitation
{
    /**
     * @Id
     * @GeneratedValue(strategy="UUID")
     * @Column(type="guid")
     * @var string
     */
    private $id;
    
    /**
     * @ManyToOne(targetEntity="App\Models\User", inversedBy="invitations", fetch="EAGER")
     * @JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     * @var App\Models\User
     */
    private $user;
    
    /**
     * @ManyToOne(targetEntity="App\Models\Family", inversedBy="invitations", fetch="EAGER")
     * @JoinColumn(name="family_id", referencedColumnName="id", onDelete="CASCADE")
     * @var App\Models\Family
     */
    private $family;
    
    /**
     * @ManyToOne(targetEntity="App\Models\User", fetch="EAGER")
     * @JoinColumn(name="created_by_id", referencedColumnName="id", onDelete="CASCADE")
     * @var App\Models\User
     */
    private $createdBy;

    /**
     * The Unix timestamp when this invitation was created
     *
     * @Column(type="integer")
     * @var int
     */
    private $created;

    public function __construct(User $user, User $createdBy)
    {
        if (!$createdBy->getFamily()) {
            throw new \InvalidArgumentException(
                'User is not currently in a family'
            );
        }
        
        $family = $createdBy->getFamily();
        if (!$family->canInviteMembers()) {
            throw new \InvalidArgumentException(
                'Family has too many outstanding invitations'
            );
        }
        
        $this->user = $user;
        $user->addInvitation($this);
        
        $this->family = $family;
        $family->addInvitation($this);
        
        $this->createdBy = $createdBy;
        
        $now = new \DateTime("now");
        $this->created = $now->getTimestamp();
    }

    public function getId()
    {
        return $this->id;
    }
    
    public function getUser()
    {
        return $this->user;
    }
    
    public function getFamily()
    {
        return $this->family;
    }
    
    public function getCreatedBy()
    {
        return $this->createdBy;
    }
}
