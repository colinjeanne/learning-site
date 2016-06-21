<?php namespace App\Middleware;

use App\Auth\Constants;
use App\Auth\JwtAuthorizer;
use App\Http\Session;
use App\Models\User;
use Doctrine\Common\Persistence\ObjectManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class OAuthMiddleware
{
    const ID_TOKEN_SESSION_KEY = 'id_token_session_key';

    private $db;
    private $jwtAuthorizer;
    private $log;
    private $session;

    public function __construct(
        LoggerInterface $log,
        ObjectManager $db,
        JwtAuthorizer $jwtAuthorizer,
        Session $session
    ) {
        $this->log = $log;
        $this->db = $db;
        $this->jwtAuthorizer = $jwtAuthorizer;
        $this->session = $session;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        if ($request->getAttribute(Constants::CURRENT_USER_KEY)) {
            return $next($request, $response);
        }

        if (!$this->session->has(self::ID_TOKEN_SESSION_KEY)) {
            $this->log->info('Access token not present in the session');
            return $next($request, $response);
        }

        $this->log->info('Attempting session authentication');

        $accessToken = $this->session->get(self::ID_TOKEN_SESSION_KEY);
        $this->log->info('Access token is present');

        try {
            $claims = $this->jwtAuthorizer->getClaims($accessToken);
        } catch (\Exception $e) {
            $this->log->info('Invalid token');
            return $next($request, $response);
        }

        if (isset($claims['iss']) && isset($claims['sub'])) {
            $this->log->info('Claims found');
            $currentUser = $this->db->getRepository(User::class)
                ->findByIssuerAndSubject(
                    $claims['iss'],
                    $claims['sub']
                );
            if (isset($currentUser)) {
                $request = $request->withAttribute(
                    Constants::CURRENT_USER_KEY,
                    $currentUser
                );
            } else {
                $this->log->info('No claims found');
            }
        }

        return $next($request, $response);
    }
}
