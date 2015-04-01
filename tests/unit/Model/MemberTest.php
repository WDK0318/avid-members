<?php

namespace Avid\CandidateChallenge\Model;

use Avid\CandidateChallenge\Model\Address;
use Avid\CandidateChallenge\Model\Email;
use Avid\CandidateChallenge\Model\Height;
use Avid\CandidateChallenge\Model\Member;
use Avid\CandidateChallenge\Model\Weight;

/**
 * @covers \Avid\CandidateChallenge\Model\Member
 *
 * @uses \Avid\CandidateChallenge\Model\Address
 * @uses \Avid\CandidateChallenge\Model\Height
 * @uses \Avid\CandidateChallenge\Model\Weight
 * @uses \Avid\CandidateChallenge\Model\Email
 *
 * @author Kevin Archer <kevin.archer@avidlifemedia.com>
 */
final class MemberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    private $fixtures;

    protected function setUp()
    {
        parent::setUp();

        $this->fixtures = array(
            'username' => 'will.hoppe',
            'password' => 'well',
            'address' => new Address('Canada', 'Ontario', 'Whitechurch-Stouffville', 'A2E 3L2'),
            'date_of_birth' => new \DateTime(date('Y-m-d', strtotime('54 years ago'))),
            'limits' => 'Something Long Term',
            'height' => new Height('6\' 11"'),
            'weight' => new Weight('125 lbs'),
            'body_type' => 'Muscular',
            'ethnicity' => 'Caucasian (white)',
            'email' => new Email('ldietrich@hotmail.com'),
        );
    }

    /**
     * @test
     */
    public function it_should_create_members_class_as_expected()
    {
        $member = new Member(
            $this->fixtures['username'],
            $this->fixtures['password'],
            $this->fixtures['address'],
            $this->fixtures['date_of_birth'],
            $this->fixtures['limits'],
            $this->fixtures['height'],
            $this->fixtures['weight'],
            $this->fixtures['body_type'],
            $this->fixtures['ethnicity'],
            $this->fixtures['email']
        );

        $this->assertInstanceOf(Member::CLASS_NAME, $member);
    }

    /**
     * @test
     */
    public function it_should_properly_load_members_values()
    {
        $member = new Member(
            $this->fixtures['username'],
            $this->fixtures['password'],
            $this->fixtures['address'],
            $this->fixtures['date_of_birth'],
            $this->fixtures['limits'],
            $this->fixtures['height'],
            $this->fixtures['weight'],
            $this->fixtures['body_type'],
            $this->fixtures['ethnicity'],
            $this->fixtures['email']
        );

        $this->assertSame($this->fixtures['username'], $member->getUsername());
        $this->assertSame($this->fixtures['password'], $member->getPassword());
        $this->assertSame($this->fixtures['address'], $member->getAddress());
        $this->assertSame($this->fixtures['date_of_birth'], $member->getDateOfBirth());
        $this->assertSame($this->fixtures['limits'], $member->getLimits());
        $this->assertSame($this->fixtures['height'], $member->getHeight());
        $this->assertSame($this->fixtures['weight'], $member->getWeight());
        $this->assertSame($this->fixtures['body_type'], $member->getBodyType());
        $this->assertSame($this->fixtures['ethnicity'], $member->getEthnicity());
        $this->assertSame($this->fixtures['email'], $member->getEmail());
    }

    /**
     * @test
     */
    public function it_should_count_member_age_correctly()
    {
        $member = new Member(
            $this->fixtures['username'],
            $this->fixtures['password'],
            $this->fixtures['address'],
            $this->fixtures['date_of_birth'],
            $this->fixtures['limits'],
            $this->fixtures['height'],
            $this->fixtures['weight'],
            $this->fixtures['body_type'],
            $this->fixtures['ethnicity'],
            $this->fixtures['email']
        );

        $this->assertEquals(54, $member->getAge());
    }
}
