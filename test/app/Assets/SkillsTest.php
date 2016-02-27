<?php namespace Test\Assets;

require_once __DIR__ . '/../../../app/Assets/Skills.php';
require_once __DIR__ . '/../Utilities.php';

class SkillsTest extends \PHPUnit_Framework_TestCase
{
    public function testGetDefaultSkillsIsValid()
    {
        \App\Assets\validateSkills(\App\Assets\getDefaultSkills());
    }
    
    /**
     * @expectedException \App\Assets\SkillValidationException
     */
    public function testNonArrayIsInvalid()
    {
        \App\Assets\validateSkills(0);
    }
    
    /**
     * @expectedException \App\Assets\SkillValidationException
     */
    public function testAllSkillsMustHaveFourElements()
    {
        $skills = \Test\cloneArray(\App\Assets\getDefaultSkills());
        $skills[1] = array_slice($skills[1], 0, 3);
        \App\Assets\validateSkills($skills);
    }
    
    /**
     * @expectedException \App\Assets\SkillValidationException
     */
    public function testTooManyGoals()
    {
        $skills = \Test\cloneArray(\App\Assets\getDefaultSkills());
        $skills[] = $skills[0];
        \App\Assets\validateSkills($skills);
    }
    
    /**
     * @expectedException \App\Assets\SkillValidationException
     */
    public function testAllGoalsMustExist()
    {
        $skills = \Test\cloneArray(\App\Assets\getDefaultSkills());
        array_pop($skills);
        \App\Assets\validateSkills($skills);
    }
    
    /**
     * @expectedException \App\Assets\SkillValidationException
     */
    public function testUnknownGoalsAreInvalid()
    {
        $skills = \Test\cloneArray(\App\Assets\getDefaultSkills());
        $skills[] = [1, 1, 999, 1];
        \App\Assets\validateSkills($skills);
    }
    
    /**
     * @expectedException \App\Assets\SkillValidationException
     */
    public function testUnknownBenchmarksAreInvalid()
    {
        $skills = \Test\cloneArray(\App\Assets\getDefaultSkills());
        $skills[] = [1, 1, 1, 999];
        \App\Assets\validateSkills($skills);
    }
    
    public function testValidSkills()
    {
        $skills = \Test\cloneArray(\App\Assets\getDefaultSkills());
        $skills[1][3] = 2;
        \App\Assets\validateSkills($skills);
    }
}
