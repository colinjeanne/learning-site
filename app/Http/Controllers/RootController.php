<?php namespace App\Http\Controllers;

use App\Auth\Constants;
use App\Auth\JwtAuthorizer;
use App\Http\Session;
use App\Middleware\OAuthMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;

class RootController
{
    private $authorizer;
    private $session;

    public function __construct(JwtAuthorizer $authorizer, Session $session)
    {
        $this->authorizer = $authorizer;
        $this->session = $session;
    }

    public function getIndex(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        $isAuthenticated = $request->getAttribute(
            Constants::CURRENT_USER_KEY
        ) ? 'true' : 'false';

        $replacements = [
            'googleClientId' => getenv('GOOGLE_CLIENT_ID'),
            'isAuthenticated' => $isAuthenticated
        ];

        return new HtmlResponse(
            $this->renderPage(
                __DIR__ . '/../../../assets/views/main.php',
                $replacements
            ),
            $response->getStatusCode(),
            $response->getHeaders()
        );
    }

    public function oauth(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        if ($this->authorizer->isOAuthRedirect($request)) {
            if ($this->authorizer->hasValidOAuthCode($request)) {
                $this->session->put(
                    OAuthMiddleware::ID_TOKEN_SESSION_KEY,
                    $this->authorizer->getIdToken()
                );

                return new RedirectResponse('/', 307);
            } else {
                $this->session->destroy();
                return $response
                    ->withStatus(403);
            }
        } else {
            $this->session->destroy();
            $authorizationUri = $this->authorizer->getOAuthUri($request);
            return new RedirectResponse($authorizationUri, 307);
        }
    }

    private function renderPage($path, $replacements = [])
    {
        $keys = array_map(
            function ($key) {
                return '{' . $key . '}';
            },
            array_keys($replacements)
        );
        $values = array_values($replacements);

        if (!ob_start(null, 0, PHP_OUTPUT_HANDLER_REMOVABLE)) {
            throw new \Exception('Could not create output buffer');
        }

        require($path);

        $contents = ob_get_contents();

        ob_end_clean();

        return str_replace($keys, $values, $contents);
    }
}
