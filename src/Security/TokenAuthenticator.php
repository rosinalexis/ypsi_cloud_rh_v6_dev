<?php

namespace App\Security;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\ExpiredTokenException;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authenticator\JWTAuthenticator;
use Symfony\Component\HttpFoundation\Request;

class TokenAuthenticator extends JWTAuthenticator
{
    public function doAuthenticate(Request $request)
    {

        $passport = parent::doAuthenticate($request);

        /**
         * @var $user User
         */
        $user =$passport->getUser();

        if( $user->getPasswordChangeDate() && $passport->getAttributes()['payload']['iat'] < $user->getPasswordChangeDate())
        {
            throw new ExpiredTokenException();
        }

        return $passport;
    }

}