<?php namespace App\Http;

class SuperGlobalSession implements Session
{
    private $started;

    public function __construct()
    {
        $this->started = session_status() === PHP_SESSION_ACTIVE;
    }

    public function has($key)
    {
        if (!$this->ensureSession()) {
            return false;
        }

        return array_key_exists($key, $_SESSION);
    }

    public function get($key)
    {
        if (!$this->ensureSession()) {
            throw new \LogicException('Session not started');
        }

        return $_SESSION[$key];
    }

    public function put($key, $value)
    {
        if ($this->ensureSession()) {
            $_SESSION[$key] = $value;
        }
    }

    public function remove($key, $value)
    {
        if ($this->has($key)) {
            unset($_SESSION[$key]);
        }
    }

    public function clear()
    {
        if ($this->started) {
            session_unset();
        }
    }

    public function destroy()
    {
        if ($this->started) {
            $this->clear();
            session_destroy();
            $this->started = false;
        }
    }

    private function ensureSession()
    {
        if (!$this->started) {
            $this->started = session_start();
        }

        return $this->started;
    }
}
