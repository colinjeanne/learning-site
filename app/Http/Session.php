<?php namespace App\Http;

interface Session
{
    public function has($key);
    public function get($key);
    public function put($key, $value);
    public function remove($key, $value);
    public function clear();
    public function destroy();
}
