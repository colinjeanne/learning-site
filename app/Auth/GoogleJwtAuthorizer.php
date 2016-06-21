<?php namespace App\Auth;

use Google_Client;
use Google_Exception;
use Google_Service_Oauth2;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

function parseQueryString($query)
{
    $result = [];
    parse_str($query, $result);
    return $result;
}

class GoogleJwtAuthorizer implements JwtAuthorizer
{
    private $googleClient;
    private $idToken;

    public function __construct(LoggerInterface $log)
    {
        $this->googleClient = new Google_Client;
        $this->googleClient->setClientId(getenv('GOOGLE_CLIENT_ID'));
        $this->googleClient->setClientSecret(getenv('GOOGLE_CLIENT_SECRET'));
        $this->googleClient->setLogger($log);
    }

    public function getClaims($jwt)
    {
        $claims = [];
        try {
            $userData = $this->googleClient->verifyIdToken($jwt);
            if ($userData) {
                $claims = self::canonicalizeIssuer($userData);
            }
        } catch (Google_Exception $e) {
        }

        return $claims;
    }

    public function isOAuthRedirect(ServerRequestInterface $request)
    {
        $query = $request->getUri()->getQuery();
        $parameters = parseQueryString($query);
        return array_key_exists('code', $parameters) ||
            array_key_exists('error', $parameters);
    }

    public function getOAuthUri(ServerRequestInterface $request)
    {
        $this->setRedirectUri($request);
        $this->googleClient->addScope(Google_Service_OAuth2::USERINFO_PROFILE);

        return $this->googleClient->createAuthUrl();
    }

    public function hasValidOAuthCode(ServerRequestInterface $request)
    {
        $query = $request->getUri()->getQuery();
        $parameters = parseQueryString($query);
        if (array_key_exists('error', $parameters)) {
            return false;
        }

        $this->setRedirectUri($request);

        try {
            $creds = $this->googleClient->fetchAccessTokenWithAuthCode(
                $parameters['code']
            );

            if (array_key_exists('id_token', $creds)) {
                $this->idToken = $creds['id_token'];
            }
        } catch (Google_Exception $e) {
            return false;
        }

        return true;
    }

    public function getIdToken()
    {
        return $this->idToken;
    }

    private function setRedirectUri(ServerRequestInterface $request)
    {
        $redirectUri = (string)$request
            ->getUri()
            ->withPath('/oauth')
            ->withQuery('')
            ->withFragment('');
        $this->googleClient->setRedirectUri($redirectUri);
    }

    private static function canonicalizeIssuer($claims)
    {
        if (array_key_exists('iss', $claims) &&
            ($claims['iss'] === 'https://accounts.google.com')) {
            $claims['iss'] = 'accounts.google.com';
        }

        return $claims;
    }
}
