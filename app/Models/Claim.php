<?php namespace App\Models;

/**
 * @Entity(repositoryClass="App\Models\ClaimRepository")
 */
class Claim
{
    /**
     * @Id
     * @Column(type="string")
     * @var string
     */
    private $claim;

    /**
     * The Unix timestamp when this claim was created
     *
     * @Column(type="integer")
     * @var int
     */
    private $created;

    /**
     * @ManyToOne(targetEntity="App\Models\User", inversedBy="claims", fetch="EAGER")
     * @JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     * @var App\Models\User
     */
    private $user;

    public function __construct($issuer, $subject)
    {
        $this->claim = $issuer . ModelConstants::CLAIM_SEPARATOR . $subject;
        
        $now = new \DateTime("now");
        $this->created = $now->getTimestamp();
    }

    public function getClaim()
    {
        return $this->claim;
    }

    public function user()
    {
        return $this->user;
    }

    public function setUser(User $user)
    {
        $user->addClaim($this);
        $this->user = $user;
    }
}
