<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2020 Sjoerd Zonneveld  <typo3@bitpatroon.nl>
 *  Date: 11-4-2020 22:46
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

namespace BPN\SupportFunctions\Tests\Unit\Service\Helpers;

use BPN\BpnHelpers\Service\CookieService;
use PHPUnit\Framework\Assert;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class CookieServiceTests extends UnitTestCase
{

    private const SecureCookieName = 'SettingX';
    private const SecureCookieValue = 'ByAX0QvIvzXS8RZi1DKG';

    /**
     * @test
     * @dataProvider dataProvider_SettingSecureCookieWorksAsExpected
     */
    Public function SettingSecureCookieWorksAsExpected($cookieValue, $expected, $usage)
    {
        $hash = $this->getHash($cookieValue);

        CookieService::setSecureCookie(
            self::SecureCookieName,
            $hash,
            $cookieValue,
            -1,
            false,
            $usage
        );

        $actual = CookieService::getSecureCookieData(self::SecureCookieName, true, false);

        Assert::assertEquals($expected, $actual);

        $newExpected = $expected;
        if ($usage == CookieService::USAGE_SINGLE) {
            $newExpected = null;
        }
        $actual = CookieService::getSecureCookieData(self::SecureCookieName);
        Assert::assertEquals($newExpected, $actual);
    }

    public function dataProvider_SettingSecureCookieWorksAsExpected()
    {
        return [
            [self::SecureCookieValue, self::SecureCookieValue, CookieService::USAGE_SINGLE],
            [self::SecureCookieValue, self::SecureCookieValue, CookieService::USAGE_MULTIPLE]
        ];

    }

    private function getHash($value)
    {
        return sha1($value);
    }
}