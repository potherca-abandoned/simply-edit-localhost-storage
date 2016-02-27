<?php
namespace Potherca\SimplyEdit;

function setHeaders($p_sServer)
{
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Methods: GET, PUT, OPTIONS');
    // "Access-Control-Allow-Credentials" is used so "Origin" cannot be a wildcard '*'
    header(sprintf('Access-Control-Allow-Origin: %s', $p_sServer));
    header('Content-type: application/json');
}

/*EOF*/