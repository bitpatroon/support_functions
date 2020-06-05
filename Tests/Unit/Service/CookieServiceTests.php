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

    private const SECURECOOKIENAME = 'SettingX';
    private const SECURECOOKIEVALUE = 'ByAX0QvIvzXS8RZi1DKG';

    /**
     * @test
     * @dataProvider dataProviderSettingSecureCookieWorksAsExpected
     */
    public function settingSecureCookieWorksAsExpected($cookieValue, $expected, $usage)
    {
        $hash = $this->getHash($cookieValue);

        CookieService::setSecureCookie(
            self::SECURECOOKIENAME,
            $hash,
            $cookieValue,
            -1,
            false,
            $usage
        );

        $actual = CookieService::getSecureCookieData(self::SECURECOOKIENAME, true, false);

        Assert::assertEquals($expected, $actual);

        $newExpected = $expected;
        if ($usage == CookieService::USAGE_SINGLE) {
            $newExpected = null;
        }
        $actual = CookieService::getSecureCookieData(self::SECURECOOKIENAME);
        Assert::assertEquals($newExpected, $actual);
    }

    public function dataProviderSettingSecureCookieWorksAsExpected()
    {
        return [
            [self::SECURECOOKIEVALUE, self::SECURECOOKIEVALUE, CookieService::USAGE_SINGLE],
            [self::SECURECOOKIEVALUE, self::SECURECOOKIEVALUE, CookieService::USAGE_MULTIPLE]
        ];
    }

    private function getHash($value)
    {
        return sha1($value);
    }
}
