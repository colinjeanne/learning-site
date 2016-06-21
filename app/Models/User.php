<?php namespace App\Models;

use Doctrine\Common\Collections\ArrayCollection;

function createClaimComparison(Claim $claim)
{
    return function ($key, Claim $otherClaim) use ($claim) {
        return $otherClaim->getClaim() === $claim->getClaim();
    };
}

/**
 * @Entity(repositoryClass="App\Models\UserRepository")
 * @Table(name="Users")
 */
class User
{
    const MAX_NUMBER_OF_CLAIMS = 5;

    /**
     * @Id
     * @GeneratedValue
     * @Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @Column(type="string", length=255, nullable=false)
     * @var string
     */
    private $name;

    /**
     * @OneToMany(targetEntity="App\Models\Claim", mappedBy="user")
     * @var App\Models\Claim[]
     */
    private $claims;

    /**
     * @ManyToOne(targetEntity="App\Models\Family", inversedBy="users")
     * @JoinColumn(name="family_id", referencedColumnName="id")
     * @var App\Models\Family
     */
    private $family;

    /**
     * @OneToMany(targetEntity="App\Models\FamilyInvitation", mappedBy="user")
     * @var App\Models\FamilyInvitations[]
     */
    private $invitations;

    /**
     * The Unix timestamp when this user was created
     *
     * @Column(type="integer")
     * @var int
     */
    private $created;

    public function __construct()
    {
        $this->name = '';
        $this->claims = new ArrayCollection();
        $this->invitations = new ArrayCollection();

        $now = new \DateTime("now");
        $this->created = $now->getTimestamp();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        if (!is_string($name) || (strlen($name) > 255)) {
            throw new \InvalidArgumentException('Invalid user name');
        }

        $this->name = $name;
    }

    public function addClaim(Claim $claim)
    {
        $comparer = createClaimComparison($claim);
        if (!$this->claims->exists($comparer)) {
            if (count($this->claims) == self::MAX_NUMBER_OF_CLAIMS) {
                throw new \InvalidArgumentException('Too many claims');
            }

            $this->claims[] = $claim;
        }
    }

    public function getFamily()
    {
        return $this->family;
    }

    public function setFamily(Family $family)
    {
        if ($this->family && !$this->family->isEmpty()) {
            throw new \InvalidArgumentException('Already part of a family');
        }

        $this->family = $family;
    }

    public function getInvitations()
    {
        return $this->invitations;
    }

    public function addInvitation(FamilyInvitation $invitation)
    {
        $this->invitations[] = $invitation;
    }
}
