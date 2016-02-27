<?php

namespace Potherca\SimplyEdit;

$sProjectRoot = dirname(__DIR__);

require($sProjectRoot . '/vendor/autoload.php');
require($sProjectRoot . '/src/bootstrap.php');

setHeaders('http://localhost:8000');

$aEnvironment = fetchEnvironmentVariables($sProjectRoot);

$oFileSystem = createFileSystem($aEnvironment);

/* PUT data comes in on the stdin stream */
$rInputStream = fopen('php://input', 'r');
$aResponse = buildResponse($oFileSystem, $rInputStream);
//fclose($rInputStream);

//@FIXME: There's a bug in SimplyEdit that causes the "Saving" modal not to be closed in the response code is not 200
//http_response_code($aResponse['code']);

echo $aResponse['body'];

exit;

/*EOF*/
