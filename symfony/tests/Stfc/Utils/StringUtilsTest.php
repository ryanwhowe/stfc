<?php

namespace App\Tests\Stfc\Utils;

use App\Stfc\Utils\StringUtils;
use PHPUnit\Framework\TestCase;

class StringUtilsTest extends TestCase {

    /**
     * @test
     * @dataProvider reduceIdsDataProvider
     *
     * @param array $input
     * @param array $expected
     */
    public function reduceIds(array $input, array $expected) {
        $actual = StringUtils::reduceIds($input);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @dataProvider reduceTopLevelIdsDataProvider
     * @param array $input
     * @param array $expected
     */
    public function reduceTopLevelIds(array $input, array $expected){
        $actual = StringUtils::reduceTopLevelIds($input);
        $this->assertEquals($expected, $actual);
    }

    public function reduceTopLevelIdsDataProvider(): array {
        return [
            'Basic Reduction, single dimension'   => [
                [['id' => 14]],
                ['14' => 1],
            ],
            'Basic Reduction, multiple dimension' => [
                [
                    ['id'     => 14,
                    'things' => [
                        ['id' => 14],
                        ['id' => 14]
                    ]],
                ],
                ['14' => 1],
            ],
            'Multiple Top Level, multiple dimension' => [
                [
                    ['id'     => 14,
                     'things' => [
                         ['id' => 14],
                         ['id' => 14]
                     ],],
                    ['id'     => 14,
                     'things' => [
                         ['id' => 14],
                         ['id' => 14]
                     ],],
                    ['id'     => 14],
                    'things' => [
                        ['id' => 14],
                        ['id' => 14]
                    ],
                ],
                ['14' => 3]
            ]
        ];
    }

    public function reduceIdsDataProvider(): array {
        return [
            'Basic Reduction, single dimension'   => [
                [['id' => 14]],
                ['14' => 1],
            ],
            'Basic Reduction, multiple dimension' => [
                [
                    ['id'     => 14,
                    'things' => [
                        ['id' => 14],
                        ['id' => 14]
                    ]],
                ],
                ['14' => 3],
            ],
            'Multiple Top Level, multiple dimension' => [
                [
                    ['id'     => 14,
                     'things' => [
                         ['id' => 14],
                         ['id' => 14]
                     ],],
                    ['id'     => 14,
                     'things' => [
                         ['id' => 14],
                         ['id' => 14]
                     ],],
                    ['id'     => 14],
                    'things' => [
                        ['id' => 14],
                        ['id' => 14]
                    ],
                ],
                ['14' => 9]
            ]
        ];
    }

}
