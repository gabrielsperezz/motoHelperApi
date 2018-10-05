<?php

namespace MotoHelper\Helper;

use Symfony\Component\HttpFoundation\Request;

class RequestParamsParser
{
    public static function toArray(Request $request)
    {
        return $data = array_merge_recursive($request->query->all(),$request->request->all(),['request-content'=> $request->getContent()]);
    }
}
