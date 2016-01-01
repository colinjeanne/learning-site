<?php namespace App\Http\Controllers;

use Zend\Diactoros\Response\HtmlResponse;

class RootController
{
    public function getIndex($request, $response, $args)
    {
        return new HtmlResponse(
            $this->renderPage(__DIR__ . '/../../../assets/views/main.php'),
            $response->getStatusCode(),
            $response->getHeaders()
        );
    }
    
    private function renderPage($path)
    {
        if (!ob_start(null, 0, PHP_OUTPUT_HANDLER_REMOVABLE)) {
            throw new \Exception('Could not create output buffer');
        }
        
        require($path);
        
        $contents = ob_get_contents();
        
        ob_end_clean();
        
        return $contents;
    }
}
