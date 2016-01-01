<?php namespace App\Models;

class ModelConstants
{
    /**
     * Separates the claim issuer from the user Id relative to the issuer. The
     * issuer member of an Id token is a URL which does not contain either a
     * query string or a fragment identifier. This separator, therefore, cannot
     * occur within the issuer URL.
     */
    const CLAIM_SEPARATOR = '#';
}
