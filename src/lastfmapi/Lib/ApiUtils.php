<?php
namespace LastFmApi\Lib;

/**
 * Description of Authentication
 *
 * @author Marcos Peña
 */
trait ApiUtils
{
    

    /*
     * Generates the api signature for use in api calls that require write access
     * @access protected
     * @return string
     */
    static public function apiSig($apiSecret, $vars)
    {
        ksort($vars);
        $signature = '';
        foreach ($vars as $name => $value) {
            $signature .= $name . $value;
        }
        $signature .= $apiSecret;
        $hashedSignature = md5($signature);

        return $hashedSignature;
    }
}
