<?php
namespace Potherca\SimplyEdit;

use Aws\S3\S3Client;
use Integral\Flysystem\Adapter\PDOAdapter;
use League\Flysystem\Adapter\Local;
use League\Flysystem\AdapterInterface;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;
use PDO;

/**
 * @param $aEnvironment
 * 
 * @return Filesystem
 *
 */
function createFileSystem($aEnvironment)
{
    $oFileSystem = null;

    $oAdapter = null;

    if ($aEnvironment['AWS_BUCKET'] !== false
        && $aEnvironment['AWS_KEY'] !== false
        && $aEnvironment['AWS_SECRET'] !== false
    ) {
        $oClient = new S3Client([
            'credentials' => [
                'key' => $aEnvironment['AWS_KEY'],
                'secret' => $aEnvironment['AWS_SECRET'],
            ],
            'region' => 'your-region',
            'version' => 'latest|version',
        ]);

        $oAdapter = new AwsS3Adapter($oClient, $aEnvironment['AWS_BUCKET']);
    } elseif ($aEnvironment['DATABASE_URL'] !== false) {
        $sTableName = 'files';

        $aParts = parse_url($aEnvironment['DATABASE_URL']);

        $oPdo = new PDO(
            sprintf('mysql:dbname=%s;host=%s', substr($aParts['path'], 1), $aParts['host']),
            $aParts['user'],
            $aParts['pass']
        );

        $bTableExists = $oPdo->query(sprintf('DESCRIBE `%s`', $sTableName));
        /* Make PDO throw Exceptions when Errors occur */
        $oPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if ($bTableExists === false) {
            $sQuery = sprintf('
                CREATE TABLE `%s` (
                    id int(11) NOT NULL AUTO_INCREMENT,
                    path varchar(255) NOT NULL,
                    type enum(\'file\',\'dir\') NOT NULL,
                    contents longblob,
                    size int(11) NOT NULL DEFAULT 0,
                    mimetype varchar(127),
                    timestamp int(11) NOT NULL DEFAULT 0,
                    PRIMARY KEY (id),
                    UNIQUE KEY path_unique (path)
                );',
                $sTableName
            );
            $oPdo->query($sQuery);
        }

        $oAdapter = new PDOAdapter($oPdo, $sTableName);
    } elseif ($aEnvironment['SERVER_ADDRESS'] === '127.0.0.1') {
        $oAdapter = new Local($aEnvironment['PROJECT_ROOT'] . '/data');
    } else {
        throw new \UnexpectedValueException(
            'Can not initialise filesystem. Environmental settings not found and not on localhost',
            500
        );
    }
    if ($oAdapter instanceof AdapterInterface) {
        $oFileSystem = new Filesystem($oAdapter);
    }

    return $oFileSystem;
}

/*EOF*/