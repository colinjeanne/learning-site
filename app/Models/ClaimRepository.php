<?php namespace App\Models;

use Doctrine\ORM\EntityRepository;

class ClaimRepository extends EntityRepository
{
    public function findByIssuerAndSubject($issuer, $subject)
    {
        $claim = $issuer . ModelConstants::CLAIM_SEPARATOR . $subject;
        return $this->findOneBy(['claim' => $claim]);
    }
}
