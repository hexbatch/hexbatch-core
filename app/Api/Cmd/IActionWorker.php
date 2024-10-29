<?php

namespace App\Api\Cmd;

/**
 * passed a param of the @see \App\Api\Cmd\IActionParams but a derived class
 */
interface IActionWorker
{
    public function doWork( $params): IActionReturn;
}
