<?php

namespace App\Utils;

class UserRoleUtil
{
    public static function sellerRoles(){
        return (auth()->check() && auth()->user()->roles == "seller");
    }

    public static function sellerStrict(){
        if(auth()->check() && auth()->user()->roles != "seller"){
            throw new \Exception("Unauthorization 403");
        }
    }
}
