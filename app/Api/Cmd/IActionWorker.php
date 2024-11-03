<?php

namespace App\Api\Cmd;

/**
 * passed a param of the @see IActionParams but a derived class
 */
interface IActionWorker
{
    public static function doWork( $params): IActionWorkReturn;
}
