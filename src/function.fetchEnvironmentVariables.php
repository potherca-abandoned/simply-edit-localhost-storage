<?php

/**
 * @param $sProjectRoot
 * @return array
 */
function fetchEnvironmentVariables($sProjectRoot)
{
    $aEnvironment = [
        'AWS_BUCKET' => getenv('S3_BUCKET_NAME'),
        'AWS_KEY' => getenv('AWS_ACCESS_KEY_ID'),
        'AWS_SECRET' => getenv('AWS_SECRET_ACCESS_KEY'),
        'DATABASE_URL' => getenv('DATABASE_URL'),
        'PROJECT_ROOT' => $sProjectRoot,
        'SERVER_ADDRESS' => $_SERVER['SERVER_ADDR'],
    ];

    return $aEnvironment;
}

/*EOF*/