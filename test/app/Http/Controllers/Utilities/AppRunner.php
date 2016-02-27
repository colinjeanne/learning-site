<?php namespace Test\Http\Controllers\Utilities;

use App\App;
use App\Models\Child;
use App\Models\Family;
use App\Models\FamilyInvitation;
use App\Models\User;
use Doctrine\ORM\Tools\SchemaTool;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Stream;
use Zend\Diactoros\Uri;

class AppRunner
{
    private $app;
    private $database;
    private $request;
    
    public function __construct()
    {
        $this->app = new App();
        $this->request = ServerRequestFactory::fromGlobals([], [], [], [], []);
        
        $reflectedContainer = new \ReflectionProperty(App::class, 'container');
        $reflectedContainer->setAccessible(true);
        $container = $reflectedContainer->getValue($this->app);
        $this->database = $container->get('doctrine');
        
        $metadata = $this->database->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($this->database);
        $schemaTool->createSchema($metadata);
    }
    
    public function run()
    {
        return $this->app->run($this->request);
    }
    
    public function createFamily()
    {
        $family = new Family();
        
        $this->database->persist($family);
        $this->database->flush();
        
        return $family;
    }
    
    public function createUser($name, Family $family = null)
    {
        $user = new User();
        $user->setName($name);
        
        if ($family) {
            $family->addMember($user);
        }
        
        $this->database->persist($user);
        $this->database->flush();
        
        return $user;
    }
    
    public function createFamilyInvitation(
        User $user,
        User $createdBy
    ) {
        $invitation = new FamilyInvitation($user, $createdBy);
        
        $this->database->persist($invitation);
        $this->database->flush();
        
        return $invitation;
    }
    
    public function createChild($name, Family $family)
    {
        $child = new Child($name);
        $family->addChild($child);
        
        $this->database->persist($child);
        $this->database->flush();
        
        return $child;
    }
    
    public function setAuthenticated(Family $family = null)
    {
        $currentUser = $this->createUser('currentUser', $family);
        $this->authenticateAs($currentUser);
        
        return $currentUser;
    }
    
    public function authenticateAs(User $user)
    {
        $this->request = $this->request->withAttribute(
            \App\Middleware\AuthenticationMiddleware::CURRENT_USER_KEY,
            $user
        );
    }
    
    public function setAcceptHeader($acceptHeader)
    {
        $this->request = $this->request->withHeader(
            'Accept',
            $acceptHeader
        );
    }
    
    public function setRequestMethod($method)
    {
        $this->request = $this->request->withMethod($method);
    }
    
    public function setRequestPath($path)
    {
        $this->request = $this->request->withUri(
            new Uri('http://example.com' . $path)
        );
    }
    
    public function setBody($body, $contentType)
    {
        if ($contentType === 'application/json') {
            $body = json_encode($body);
        }
        
        $stream = new Stream('php://memory', 'rw');
        $stream->write($body);
        $this->request = $this->request
            ->withBody($stream)
            ->withHeader('Content-Type', $contentType);
    }
    
    public function getResponseJson(Response $response)
    {
        $stream = $response->getBody();
        $contents = (string)$stream;
        $json = json_decode($contents, true);
        
        
        if (json_last_error() !== 0) {
            throw \Exception('Response is not JSON');
        }
        
        return $json;
    }
}
