<?php

namespace Potherca\SimplyEdit;

use League\Flysystem\Exception;

set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return;
    }
    throw new \ErrorException($message, 0, $severity, $file, $line);
});

set_exception_handler(function (\Exception $p_oException) {

    $aResponse = [
        'error' => [],
        'code' => 500,
    ];

    $oException = $p_oException;
    if ($p_oException->getPrevious() !== null) {
        $oException = $p_oException->getPrevious();
    }

    $sExceptionClass = get_class($oException);

    switch($sExceptionClass) {
        case $oException instanceof Exception:
        case \PDOException::class:
            $aResponse['error']['code'] = 502;
            break;
    }

    if ((int) $p_oException->getCode() > 400) {
        $aResponse['error']['code'] = $p_oException->getCode();
    }

    $aResponse['error']['message'] = $oException->getMessage();
    $aResponse['error']['type'] = $sExceptionClass;

    if ($_SERVER['SERVER_ADDR'] === '127.0.0.1') {
        $aResponse['error']['trace'] = $oException->getTrace();
    }

    //$sError = sprintf($_SERVER['SERVER_PROTOCOL'] . ' %s %s', $iErrorCode, $sErrorMessage);
    //header($sError, true, $iErrorCode);
    //@FIXME: There's a bug in SimplyEdit that causes the "Saving" modal not to be closed in the response code is not 200
    http_response_code($aResponse['code']);

    $sResponse = json_encode($aResponse,
        JSON_FORCE_OBJECT | JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR
    );

    echo $sResponse;
});

