<?php
namespace Potherca\SimplyEdit;

use League\Flysystem\Filesystem;

/**
 * @param Filesystem $oFileSystem
 * @param $rInputStream
 *
 * @return array
 */
function buildResponse(Filesystem $oFileSystem, $rInputStream)
{
    $aResponse = [
        'body' => '{}',
        'code' => 200,
    ];

    $sPath = array_key_exists('PATH_INFO', $_SERVER) ? $_SERVER['PATH_INFO'] : '';
    $sRequestMethod = $_SERVER['REQUEST_METHOD'];

    switch ($sRequestMethod) {
        case 'PUT':
            $sProject = guessProjectFromPath($sPath);
            $sFilePath = sprintf('%s.json', $sProject);

            $aResponse['body'] = '';

            if ($oFileSystem->has($sFilePath) === false) {
                $aResponse['code'] = 201;
                $bSuccess = $oFileSystem->writeStream($sFilePath, $rInputStream);
            } else {
                $aResponse['code'] = 204;
                $bSuccess = $oFileSystem->updateStream($sFilePath, $rInputStream);
            }

            if ($bSuccess === false) {
                throw new \RuntimeException('Writing contents failed', 502);
            }

            break;
        case 'OPTIONS':
            // No-op
            break;

        case 'GET':
            $sProject = guessProjectFromPath($sPath);
            $sFilePath = sprintf('%s.json', $sProject);

            if ($oFileSystem->has($sFilePath)) {
                $aResponse['body'] = $oFileSystem->read($sFilePath);
            }
            break;

        default:
            throw new \LogicException(
                sprintf('Method "%s" is not allowed. Use one of GET, PUT, OPTIONS', $sRequestMethod),
                405
            );
            //throw new \LogicException(
            //    sprintf('Support for Request Method "%s" has not been implemented', $sRequestMethod),
            //    501
            //);
            break;
    }
    return $aResponse;
}

/*EOF*/