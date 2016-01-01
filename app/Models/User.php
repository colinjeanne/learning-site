<?php namespace App\Models;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table(name="Users")
 */
class User
{
    /**
     * @Id
     * @GeneratedValue
     * @Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @OneToMany(targetEntity="App\Models\Claim", mappedBy="user")
     * @var App\Models\Claim[]
     */
    private $claims;
    
    /**
     * @ManyToOne(targetEntity="App\Models\Family", inversedBy="users")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     * @var App\Models\Family
     */
    private $family;

    /**
     * The Unix timestamp when this user was created
     *
     * @Column(type="integer")
     * @var int
     */
    private $created;

    public function __construct()
    {
        $this->claims = new ArrayCollection();
        
        $now = new \DateTime("now");
        $this->created = $now->getTimestamp();
    }

    public function getId()
    {
        return $this->id;
    }

    public function addClaim(Claim $claim)
    {
        if (!$this->claims->contains(function ($e) {
            return $e->getClaim() === $user->getClaim();
        })) {
            $this->claims[] = $claim;
        }
    }
    
    public function family()
    {
        return $this->family;
    }
    
    public function addToFamily(Family $family)
    {
        if ($this->family) {
            throw new \InvalidArgumentException('Already part of a family');
        }
        
        $family->addUser($this);
        $this->family = $family;
    }
}
