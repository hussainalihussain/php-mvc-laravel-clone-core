<?php

namespace app\core;

class Session
{
    protected static $flashPrefix = '_flash_';

    public function __construct()
    {
        session_start();
        $this->markRemovableFlashMessages();
    }

    public function markRemovableFlashMessages()
    {
        $flashMessages = $_SESSION[self::$flashPrefix] ?? [];

        foreach ($flashMessages as &$flashMessage)
        {
            $flashMessage['remove'] = true;
        }

        $_SESSION[self::$flashPrefix] = $flashMessages;
    }

    public function removeMarkedFlashMessages()
    {
        $flashMessages = $_SESSION[self::$flashPrefix] ?? [];

        foreach ($flashMessages as $key=> &$flashMessage)
        {
            if($flashMessage['remove'])
            {
                unset($flashMessages[$key]);
            }
        }

        $_SESSION[self::$flashPrefix] = $flashMessages;
    }

    public function setFlash(string $key, $value)
    {
        $_SESSION[self::$flashPrefix][$key] = [
            'value'=> $value,
            'remove'=> false
        ];
    }

    public function getFlash(string $key)
    {
        return $_SESSION[self::$flashPrefix][$key]['value'] ?? false;
    }


    public static function set(string $key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key)
    {
        return $_SESSION[$key] ?? false;
    }

    public static function remove($key): bool
    {
        unset($_SESSION[$key]);

        return true;
    }

    public function __destruct()
    {
        $this->removeMarkedFlashMessages();
    }
}