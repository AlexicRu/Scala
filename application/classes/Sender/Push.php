<?php defined('SYSPATH') or die('No direct script access.');

class Sender_Push extends Sender
{
    public function send($message)
    {
        return false;
    }
}