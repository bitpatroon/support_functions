<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Sjoerd Zonneveld <typo3@bitpatroon.nl>
 *  Date: 26-8-2016 15:29
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

namespace BPN\SupportFunctions\Service;

use InvalidArgumentException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class CookieService
{
    const SECRET = '5ubRoa9a59qoDgSW7Jr8';

    const FIELD_HASH = 'hash';
    const FIELD_KEY = 'key';
    const FIELD_EXPIRATION = 'expires_on';
    const FIELD_CONTENT = 'content';
    const FIELD_ISUSED = 'isused';
    const FIELD_USAGE = 'usage';
    const FIELD_PREFIX = 'prefix';

    const USAGE_DONTCARE = 0;
    const USAGE_SINGLE = 1;
    const USAGE_MULTIPLE = 2;
    const COOKIE_FE_TYPO_USER = 'fe_typo_user';
    const COOKIE_PHPSESSID = 'PHPSESSID';
    const COOKIE_SPLSAMLID = 'splsamlid';
    const COOKIE_SPLSAMLAUTHT = 'splsamlautht';

    /**
     * Gets the cookie
     * @param string $cookieName
     * @param bool   $mustBeValid                     true to check the
     * @param bool   $singleUseOnly                   true to allow usage only once.
     * @param int    $numberOfSecondsBeforeExpiration The amount of seconds before the cookie expires
     * @return mixed|null The deserialized cookie values.
     */
    public static function getSecureCookieData(
        $cookieName,
        $mustBeValid = true,
        $singleUseOnly = true,
        $numberOfSecondsBeforeExpiration = 3600
    ) {
        if (empty($cookieName)) {
            throw new InvalidArgumentException('Invalid value for $cookieName', 1472218963);
        }
        if (empty($numberOfSecondsBeforeExpiration) || $numberOfSecondsBeforeExpiration < 0) {
            $numberOfSecondsBeforeExpiration = 3600;
        }

        $attributes = null;

        // restore the settings from the cookie
        if (!empty($_COOKIE[$cookieName])) {
            $message = $_COOKIE[$cookieName];
            $decodedMessage = base64_decode($message);
            if (!empty($decodedMessage)) {
                $attributes = unserialize($decodedMessage, ['allowed_classes' => false]);
            }
        }

        if (empty($attributes[self::FIELD_CONTENT])) {
            return null;
        }

        if ($mustBeValid) {
            $key = $attributes[self::FIELD_KEY];
            $hash = $attributes[self::FIELD_HASH];

            /** @var VerificationCodeService $verificationCodeService */
            $verificationCodeService = GeneralUtility::makeInstance(VerificationCodeService::class);
            if (!$verificationCodeService->isValid($hash, $key, $numberOfSecondsBeforeExpiration, self::SECRET)) {
                return null;
            }
        }

        if ($singleUseOnly || ((int)$attributes[self::FIELD_USAGE] == self::USAGE_SINGLE)) {
            if (isset($attributes[self::FIELD_ISUSED]) && (int)$attributes[self::FIELD_ISUSED] == 1) {
                return null;
            }
            self::markCookieUsed($cookieName);
        }
        return unserialize($attributes[self::FIELD_CONTENT], ['allowed_classes' => false]);
    }

    /**
     * Method sets the attributes
     * @param string $cookieName
     * @internal param $attributes
     */
    public static function markCookieUsed($cookieName)
    {

        // restore the settings from the cookie
        if (!empty($_COOKIE[$cookieName])) {
            $message = $_COOKIE[$cookieName];
            $decodedMessage = base64_decode($message);
            if (!empty($decodedMessage)) {
                $attributes = unserialize($decodedMessage, ['allowed_classes' => false]);
            }
        }

        if (!empty($attributes)) {
            $attributes[self::FIELD_ISUSED] = 1;
            $cookieExpiration = (int)$attributes[self::FIELD_EXPIRATION];
            if (empty($cookieExpiration)) {
                $cookieExpiration = time() - 3600;
            }

            // update the message
            $message = base64_encode(serialize($attributes));
            setcookie($cookieName, $message, $cookieExpiration, '/', null);
            $_COOKIE[$cookieName] = $message;

            return;
        }

        // a cookie was present, but empty. So remove.
        if (isset($_COOKIE[$cookieName])) {
            unset($_COOKIE[$cookieName]);
            setcookie($cookieName, null, -1, '/', null);
        }
    }

    /**
     * Method sets the attributes
     * @param string $cookieName                          the name of the cookie
     * @param string $hashKey                             the key to determine the hash
     * @param mixed  $data                                the data to store
     * @param int    $cookieExpirationSeconds             the amount of seconds before the cookie expires.
     *                                                    Make 0 to make (browser)session.
     * @param int    $numberOfSecondsBeforeHashExpiration the amount of seconds until the hash expires
     * @param bool   $expire                              true to remove the cookie
     * @param        $usage
     * @internal param $attributes
     */
    public static function setSecureCookie(
        $cookieName,
        $hashKey,
        $data,
        $cookieExpirationSeconds = -1,
        $expire = false,
        $usage = self::USAGE_DONTCARE
    ) {
        if (empty($cookieName)) {
            throw new InvalidArgumentException(
                'Invalid value for $cookieName. Cannot be empty',
                1472218980
            );
        }
        if (empty($hashKey)) {
            throw new InvalidArgumentException(
                'Invalid value for $key. Cannot be empty',
                1472218996
            );
        }

        if ($expire) {
            setcookie($cookieName, null, time() - 3600, '/', null);
            unset($_COOKIE[$cookieName]);
            return;
        }

        if ($cookieExpirationSeconds < 0) {
            $numberOfSecondsBeforeHashExpiration = 86400 * 365;
        }

        $attributes[self::FIELD_PREFIX] = md5(time());
        $attributes[self::FIELD_KEY] = $hashKey;
        /** @var VerificationCodeService $verificationCodeService */
        $verificationCodeService = GeneralUtility::makeInstance(VerificationCodeService::class);
        $attributes[self::FIELD_HASH] = $verificationCodeService->createVerificationCode(
            $hashKey,
            $numberOfSecondsBeforeHashExpiration,
            self::SECRET
        );
        $attributes[self::FIELD_CONTENT] = serialize($data);
        if (!empty($usage)) {
            $attributes[self::FIELD_USAGE] = (int)$usage;
        }

        $cookieExpiration = null;
        $cookieExpirationSeconds = (int)$cookieExpirationSeconds;
        if (empty($cookieExpirationSeconds) || $cookieExpirationSeconds < 0) {
            $cookieExpirationSeconds = 86400;
        }

        if (!empty($cookieExpirationSeconds)) {
            $attributes[self::FIELD_EXPIRATION] = time() + $cookieExpirationSeconds;
            $cookieExpiration = time() + $cookieExpirationSeconds;
        }

        $message = base64_encode(serialize($attributes));

        setcookie($cookieName, $message, $cookieExpiration, '/', null);
        $_COOKIE[$cookieName] = $message;
    }

    /**
     * Ensures a cookies has either a value or is removed from the request.
     * @param string $id the ID of the cookie
     */
    public static function deleteCookie($id)
    {
        if (empty($id)) {
            throw new InvalidArgumentException('Invalid value for $id', 1586637692);
        }
        unset($_COOKIE[$id]);

        setcookie($id, null, time() - 3600, '/', null);
    }
}
