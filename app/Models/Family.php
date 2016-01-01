<?php namespace App\Models;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table(name="Families")
 */
class Family
{
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
    private $users;

    /**
     * The Unix timestamp when this family was created
     *
     * @Column(type="integer")
     * @var int
     */
    private $created;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        
        $now = new \DateTime("now");
        $this->created = $now->getTimestamp();
    }

    public function getId()
    {
        return $this->id;
    }
    
    public function users()
    {
        return $this->users;
    }

    public function addUser(User $user)
    {
        if (!$this->users->contains(function ($e) {
            return $e->getId() === $user->getId();
        })) {
            $this->users[] = $user;
        }
    }
}
