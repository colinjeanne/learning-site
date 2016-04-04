<?php namespace App\Models;

require_once __DIR__ . '/../Assets/Skills.php';

/**
 * @Entity
 * @Table(name="Children")
 */
class Child
{
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
     * @ManyToOne(targetEntity="App\Models\Family", inversedBy="children")
     * @JoinColumn(name="family_id", referencedColumnName="id")
     * @var App\Models\Family
     */
    private $family;
    
    /**
     * @Column(type="string", length=1024, nullable=false)
     * @var string
     */
    private $skills;

    public function __construct($name)
    {
        $this->setName($name);
        $this->setSkills(\App\Assets\getDefaultSkills());
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
            throw new \InvalidArgumentException('Invalid child name');
        }
        
        $this->name = $name;
    }
    
    public function getFamily()
    {
        return $this->family;
    }
    
    public function setFamily(Family $family)
    {
        $this->family = $family;
    }
    
    public function getSkills()
    {
        return json_decode($this->skills, true);
    }
    
    public function setSkills($skills)
    {
        \App\Assets\validateSkills($skills);
        
        $this->skills = json_encode($skills);
    }
}
