<?php namespace Test\Models;

use App\Models\UserRepository;

class UserRepositoryTest extends \PHPUnit_Framework_TestCase
{
    public static function setupBeforeClass()
    {
        date_default_timezone_set('UTC');
    }

    public function testNewClaim()
    {
        $repository = $this->createRepository(false, false);
        $currentUser = $repository->findByIssuerAndSubject('iss', 'sub');

        $this->assertNotNull($currentUser);
    }

    public function testExistingClaimWithoutUser()
    {
        $repository = $this->createRepository(true, false);
        $currentUser = $repository->findByIssuerAndSubject('iss', 'sub');

        $this->assertNotNull($currentUser);
    }

    public function testExistingClaimWithExistingUser()
    {
        $repository = $this->createRepository(true, true);
        $currentUser = $repository->findByIssuerAndSubject('iss', 'sub');

        $this->assertNotNull($currentUser);
    }

    private function createRepository($hasClaim, $hasUser)
    {
        $claimRepository = $this->getMockBuilder(
            \App\Models\ClaimRepository::class
        )
            ->disableOriginalConstructor()
            ->getMock();

        if ($hasClaim) {
            $claim = new \App\Models\Claim('iss', 'sub');

            if ($hasUser) {
                $user = new \App\Models\User();
                $claim->setUser($user);
            }

            $claimRepository->method('findByIssuerAndSubject')
                ->willReturn($claim);
        }

        $entityManager = $this->getMockBuilder(
            \Doctrine\Common\Persistence\ObjectManager::class
        )->getMock();

        $entityManager->method('getRepository')
            ->willReturn($claimRepository);

        $classMetadata = new \Doctrine\ORM\Mapping\ClassMetadata('User');

        return new UserRepository($entityManager, $classMetadata);
    }
}
