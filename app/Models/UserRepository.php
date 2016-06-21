<?php namespace App\Models;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function findByIssuerAndSubject($issuer, $subject)
    {
        $user = null;
        $entityManager = $this->getEntityManager();
        $claim = $entityManager->getRepository(Claim::class)
            ->findByIssuerAndSubject($issuer, $subject);
        if (!isset($claim)) {
            $claim = new Claim($issuer, $subject);
            $entityManager->persist($claim);
        }

        if (isset($claim)) {
            $user = $claim->user();
            if (!isset($user)) {
                $user = new User();
                $entityManager->persist($user);

                $claim->setUser($user);
            }
        }

        $entityManager->flush();
        return $user;
    }
}
