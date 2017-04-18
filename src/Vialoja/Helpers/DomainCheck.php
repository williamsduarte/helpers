<?php


namespace Vialoja\Helpers;


class DomainCheck
{

    /**
     * @param null $address
     * @return string
     */
    public static function check($address = null)
    {

        $parsed_url = parse_url($address);
        if (isset($parsed_url['host'])) {
            $check = self::esip($parsed_url['host']);
            $host = $parsed_url['host'];
        }

        if (!isset($check) ){
            if ( isset( $host ) ){
                return self::domain($host);
            }else{
                return self::domain($address);
            }
        }
    }

    /**
     * @param $ip_addr
     * @return bool
     */
    private static function esip($ip_addr)
    {
        //first of all the format of the ip address is matched
        if(preg_match("/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/",$ip_addr))
        {
            //now all the intger values are separated
            $parts=explode(".",$ip_addr);
            //now we need to check each part can range from 0-255
            foreach($parts as $ip_parts)
            {
                if(intval($ip_parts)>255 || intval($ip_parts)<0)
                    return FALSE; //if number is not within range of 0-255
            }
            return TRUE;
        }
        else
            return FALSE; //if format of ip address doesn't matches
    }

    /**
     * @param $domain
     * @return string
     */
    private static function domain($domain)
    {
        $bits = explode('/', $domain);
        if ($bits[0]=='http:' || $bits[0]=='https:')
        {
            $domain= $bits[2];
        } else {
            $domain= $bits[0];
        }

        unset($bits);
        $bits = explode('.', $domain);
        $idz=count($bits);
        $idz-=3;

        if (strlen($bits[($idz+2)])==2) {
            $url=$bits[$idz].'.'.$bits[($idz+1)].'.'.$bits[($idz+2)];
        } else if (strlen($bits[($idz+2)])==0) {
            $url=$bits[($idz)].'.'.$bits[($idz+1)];
        } else {
            $url=$bits[($idz+1)].'.'.$bits[($idz+2)];
        }
        return $url;
    }

}