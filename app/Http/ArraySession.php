<?php namespace App\Http;

class ArraySession implements Session
{
    private $session = [];

    public function has($key)
    {
        return array_key_exists($key, $this->session);
    }

    public function get($key)
    {
        return $this->session[$key];
    }

    public function put($key, $value)
    {
        $this->session[$key] = $value;
    }

    public function remove($key, $value)
    {
        if ($this->has($key)) {
            unset($this->session[$key]);
        }
    }

    public function clear()
    {
        $this->session = [];
    }

    public function destroy()
    {
        $this->clear();
    }
}
