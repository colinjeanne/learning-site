<?php namespace App\Middleware;

use App\Auth\JwtAuthorizer;
use App\Models\Claim;
use App\Models\User;
use Doctrine\Common\Persistence\ObjectManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class AuthenticationMiddleware
{
    const CURRENT_USER_KEY = 'current_user_key';
    
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
                $claim = $this->db->getRepository(Claim::class)
                    ->findByIssuerAndSubject(
                        $claims['iss'],
                        $claims['sub']
                    );
                if (!isset($claim)) {
                    $this->log->info(
                        'Claim not found in database, persisting'
                    );
                    $claim = new Claim($claims['iss'], $claims['sub']);
                    $this->db->persist($claim);
                }

                if (isset($claim)) {
                    $currentUser = $claim->user();
                    if (!isset($currentUser)) {
                        $this->log->info('Generating user for claim');
                        $currentUser = new User();
                        $this->db->persist($currentUser);

                        $claim->setUser($currentUser);
                    }
                    
                    $request = $request->withAttribute(
                        self::CURRENT_USER_KEY,
                        $currentUser
                    );
                }

                $this->db->flush();
            } else {
                $this->log->info('No claims found');
            }
        }
        
        return $next($request, $response);
    }
}
