<?php


namespace App\Controllers\RPCapi;


class ErrorCodes
{
    // https://www.jsonrpc.org/specification#error_object (Коды ошибок)
    const PARSE_ERROR = -32700;
    const INVALID_REQUEST = -32600;
    const METHOD_NOT_FOUND = -32601;
    const INVALID_PARAMETERS = -32602;
    const INTERNAL_ERROR = -32603;
    const SERVER_ERROR = -32000;
}
