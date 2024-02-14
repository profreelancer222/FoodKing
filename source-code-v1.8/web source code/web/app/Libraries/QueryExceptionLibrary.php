<?php

namespace App\Libraries;


use Exception;


class QueryExceptionLibrary
{

    /**
     * @param Exception $e
     * @return string
     */
    public static function message(Exception $e): string
    {
        if (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1451) {
            return trans('all.message.resource_already_used');
        } else {
            return $e->getMessage();
        }
    }
}
