<?php

namespace App\Utils;

class UserRoleUtil
{
    public static function sellerRoles():bool{
        return (auth()->check() && auth()->user()->roles == "seller");
    }

    public static function sellerStrict(){
        if(auth()->check() && auth()->user()->roles != "seller"){
            throw new \Exception("Unauthorization 403");
        }
    }

    public static function buyerStrict(){
        if(auth()->check() && auth()->user()->roles != "buyer"){
            throw new \Exception("Unauthorization 403");
        }
    }
}
