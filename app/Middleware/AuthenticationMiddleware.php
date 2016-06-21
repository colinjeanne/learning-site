<?php namespace App\Middleware;

use App\Auth\Constants;
use App\Auth\JwtAuthorizer;
use App\Models\User;
use Doctrine\Common\Persistence\ObjectManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class AuthenticationMiddleware
{
    private $jwtAuthorizer;
    private $db;
    private $log;

    public function __construct(
        LoggerInterface $log,
        ObjectManager $db,
        JwtAuthorizer $jwtAuthorizer
    ) {
        $this->log = $log;
        $this->db = $db;
        $this->jwtAuthorizer = $jwtAuthorizer;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        if (!$request->hasHeader('Authorization')) {
            $this->log->info('Authorization header not present');
            return $next($request, $response);
        }

        $this->log->info('Attempting Id token authorization');

        $authorization = $request->getHeaderLine('Authorization');
        $this->log->info(
            'Authorization header is present',
            ['header' => $authorization]
        );

        $authInfo = explode(' ', $authorization);
        if ((count($authInfo) === 2) && ($authInfo[0] === 'Bearer')) {
            $jwt = $authInfo[1];

            try {
                $claims = $this->jwtAuthorizer->getClaims($jwt);
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
                }
            } else {
                $this->log->info('No claims found');
            }
        }

        return $next($request, $response);
    }
}
