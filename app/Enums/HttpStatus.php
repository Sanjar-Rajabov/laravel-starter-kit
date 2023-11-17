<?php

namespace App\Enums;

abstract class HttpStatus
{
    final public static function status(HttpCode $code): string
    {
        switch ($code) {
            // 1xx
            case HttpCode::CONTINUE: return 'Continue'; break;
            case HttpCode::SWITCHING_PROTOCOLS: return 'Switching Protocols'; break;
            case HttpCode::PROCESSING: return 'Processing'; break;
            // 2xx
            case HttpCode::OK: return 'Ok'; break;
            case HttpCode::CREATED: return 'Created'; break;
            case HttpCode::ACCEPTED: return 'Accepted'; break;
            case HttpCode::NON_AUTHORITATIVE_INFORMATION: return 'Non-Authoritative Information'; break;
            case HttpCode::NO_CONTENT: return 'No Content'; break;
            case HttpCode::RESET_CONTENT: return 'Reset Content'; break;
            case HttpCode::PARTIAL_CONTENT: return 'Partial Content'; break;
            case HttpCode::MULTI_STATUS: return 'Multi-Status'; break;
            // 3xx
            case HttpCode::MULTIPLE_CHOICES: return 'Multiple Choices'; break;
            case HttpCode::MOVED_PERMANENTLY: return 'Moved Permanently'; break;
            case HttpCode::FOUND: return 'Found'; break;
            case HttpCode::SEE_OTHER: return 'See Other'; break;
            case HttpCode::NOT_MODIFIED: return 'Not Modified'; break;
            case HttpCode::USE_PROXY: return 'Use Proxy'; break;
            case HttpCode::TEMPORARY_REDIRECT: return 'Temporary Redirect'; break;
            // 4xx
            case HttpCode::BAD_REQUEST: return 'Bad Request'; break;
            case HttpCode::UNAUTHORIZED: return 'Unauthorized'; break;
            case HttpCode::PAYMENT_REQUIRED: return 'Payment Required'; break;
            case HttpCode::FORBIDDEN: return 'Forbidden'; break;
            case HttpCode::NOT_FOUND: return 'Not Found'; break;
            case HttpCode::METHOD_NOT_ALLOWED: return 'Method Not Allowed'; break;
            case HttpCode::NOT_ACCEPTABLE: return 'Not Acceptable'; break;
            case HttpCode::PROXY_AUTHENTICATION_REQUIRED: return 'Proxy Authentication Required'; break;
            case HttpCode::REQUEST_TIMEOUT: return 'Request Timeout'; break;
            case HttpCode::CONFLICT: return 'Conflict'; break;
            case HttpCode::GONE: return 'Gone'; break;
            case HttpCode::LENGTH_REQUIRED: return 'Length Required'; break;
            case HttpCode::PRECONDITION_FAILED: return 'Precondition Failed'; break;
            case HttpCode::REQUEST_ENTITY_TOO_LARGE: return 'Request Entity Too Large'; break;
            case HttpCode::REQUEST_URI_TOO_LONG: return 'Request-URI Too Long'; break;
            case HttpCode::UNSUPPORTED_MEDIA_TYPE: return 'Unsupported Media Type'; break;
            case HttpCode::REQUESTED_RANGE_NOT_SATISFIABLE: return 'Requested Range Not Satisfiable'; break;
            case HttpCode::EXPECTATION_FAILED: return 'Expectation Failed'; break;
            case HttpCode::AUTHENTICATION_TIMEOUT_NOT_IN_RFC_2616: return 'Authentication Timeout (not in RFC 2616)'; break;
            case HttpCode::UNPROCESSABLE_ENTITY: return 'Unprocessable Entity'; break;
            case HttpCode::LOCKED: return 'Locked'; break;
            case HttpCode::FAILED_DEPENDENCY: return 'Failed Dependency'; break;
            case HttpCode::UPGRADE_REQUIRED: return 'Upgrade Required'; break;
            // 5xx
            case HttpCode::INTERNAL_SERVER_ERROR: return 'Internal Server Error'; break;
            case HttpCode::NOT_IMPLEMENTED: return 'Not Implemented'; break;
            case HttpCode::BAD_GATEWAY: return 'Bad Gateway'; break;
            case HttpCode::SERVICE_UNAVAILABLE: return 'Service Unavailable'; break;
            case HttpCode::GATEWAY_TIMEOUT: return 'Gateway Timeout'; break;
            case HttpCode::HTTP_VERSION_NOT_SUPPORTED: return 'HTTP Version Not Supported'; break;
            case HttpCode::VARIANT_ALSO_NEGOTIATES: return 'Variant Also Negotiates'; break;
            case HttpCode::INSUFFICIENT_STORAGE: return 'Insufficient Storage'; break;
            case HttpCode::BANDWIDTH_LIMIT_EXCEEDED: return 'Bandwidth Limit Exceeded'; break;
            case HttpCode::NOT_EXTENDED: return 'Not Extended'; break;
        }
    }
}
