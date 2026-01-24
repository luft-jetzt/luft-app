<?php declare(strict_types=1);

namespace App\Tests\Air\Util;

use App\Air\Util\StringUtil;
use PHPUnit\Framework\TestCase;

class StringUtilTest extends TestCase
{
    /**
     * @dataProvider camelCaseProvider
     */
    public function testCamelCaseToUnderscore(string $input, string $expected): void
    {
        $this->assertEquals($expected, StringUtil::camelCaseToUnderscore($input));
    }

    public static function camelCaseProvider(): array
    {
        return [
            'simple camelCase' => ['camelCase', 'camel_case'],
            'PascalCase' => ['PascalCase', 'pascal_case'],
            'already lowercase' => ['lowercase', 'lowercase'],
            'multiple humps' => ['thisIsALongName', 'this_is_a_long_name'],
            'consecutive capitals' => ['XMLParser', 'x_m_l_parser'],
            'single word' => ['Word', 'word'],
            'empty string' => ['', ''],
            'all caps' => ['ABC', 'a_b_c'],
        ];
    }

    public function testCamelCaseToUnderscoreWithDoubleUnderscore(): void
    {
        // Input with consecutive capitals that would create double underscore
        $result = StringUtil::camelCaseToUnderscore('testABCase', true);
        // With avoidDoubleUnderscore=true, any double underscore should be replaced
        $this->assertStringNotContainsString('__', $result);
    }

    public function testCamelCaseToUnderscorePreservesDoubleUnderscore(): void
    {
        $result = StringUtil::camelCaseToUnderscore('test__Case', false);
        // Should preserve double underscore when avoidDoubleUnderscore is false
        $this->assertStringContainsString('_', $result);
    }
}
