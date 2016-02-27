<?php
namespace Potherca\SimplyEdit;

/**
 * @param string $p_sPath
 *
 * @return string
 */
function guessProjectFromPath($p_sPath)
{
    $sProject = '';

    $sPath = trim((string) $p_sPath, '/');

    if (strpos($sPath, '/') !== false) {
        $aParts = explode('/', $sPath, 2);

        if (array_pop($aParts) === 'data/data.json'
            && count($aParts) === 1
            && empty($aParts[0]) === false
        ) {
            $sProject = $aParts[0];
        } else {
            throw new \UnexpectedValueException(
                sprintf('Could not guess Project Name from requested URL "%s"', $sPath),
                404
            );
        }
    }
    return $sProject;
}

/*EOF*/