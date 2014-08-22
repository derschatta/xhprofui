<?php

namespace Xhprof\GuiBundle\Tests\Model;

use Xhprof\GuiBundle\Model\DataParser;

class DataParserTest extends \PHPUnit_Framework_TestCase {

    /**
     * parse data
     *
     * @return void
     */
    public function testParsingCompleteSet() {
        $parser = new DataParser();
        $result = $parser->parse($this->getTestData());
        $this->assertInternalType('array', $result);
        $this->assertEquals($this->getExpectedData(), $result);
    }

    /**
     * get sample test data, used the sample from the xhprof package
     *
     * @return array
     */
    private function getTestData() {
        return array(
            'foo==>bar' => array(
                'ct' => 5,
                'wt' => 257,
                'cpu' => 409,
                'mu' => 4992,
                'pmu' => 0
            ),
            'foo==>strlen' => array(
                'ct' => 5,
                'wt' => 20,
                'cpu' => 25,
                'mu' => 752,
                'pmu' => 0
            ),
            'bar==>bar@1' => array(
                'ct' => 4,
                'wt' => 58,
                'cpu' => 62,
                'mu' => 3872,
                'pmu' => 0
            ),
            'bar@1==>bar@2' => array(
                'ct' => 3,
                'wt' => 22,
                'cpu' => 25,
                'mu' => 2832,
                'pmu' => 0
            ),
            'bar@2==>bar@3' => array(
                'ct' => 2,
                'wt' => 10,
                'cpu' => 13,
                'mu' => 1792,
                'pmu' => 0
            ),
            'bar@3==>bar@4' => array(
                'ct' => 1,
                'wt' => 2,
                'cpu' => 4,
                'mu' => 752,
                'pmu' => 0
            ),
            'main()==>foo' => array(
                'ct' => 1,
                'wt' => 544,
                'cpu' => 546,
                'mu' => 7104,
                'pmu' => 0
            ),
            'main()==>xhprof_disable' => array(
                'ct' => 1,
                'wt' => 1,
                'cpu' => 2,
                'mu' => 760,
                'pmu' => 0
            ),
            'main()' => array(
                'ct' => 1,
                'wt' => 811,
                'cpu' => 809,
                'mu' => 9256,
                'pmu' => 0
            )
        );
    }

    private function getExpectedData() {
        return array(
            'main()' => array(
                'ct' => 1,
                'wt' => 811,
                'cpu' => 809,
                'mu' => 9256,
                'pmu' => 0,
                'excl_wt' => 266,
                'excl_cpu' => 261,
                'excl_mu' => 1392,
                'excl_pmu' => 0
            ),
            'foo' => array(
                'ct' => 1,
                'wt' => 544,
                'cpu' => 546,
                'mu' => 7104,
                'pmu' => 0,
                'excl_wt' => 267,
                'excl_cpu' => 112,
                'excl_mu' => 1360,
                'excl_pmu' => 0
            ),
            'bar' => array(
                'ct' => 5,
                'wt' => 257,
                'cpu' => 409,
                'mu' => 4992,
                'pmu' => 0,
                'excl_wt' => 199,
                'excl_cpu' => 347,
                'excl_mu' => 1120,
                'excl_pmu' => 0
            ),
            'bar@1' => array(
                'ct' => 4,
                'wt' => 58,
                'cpu' => 62,
                'mu' => 3872,
                'pmu' => 0,
                'excl_wt' => 36,
                'excl_cpu' => 37,
                'excl_mu' => 1040,
                'excl_pmu' => 0
            ),
            'bar@2' => array(
                'ct' => 3,
                'wt' => 22,
                'cpu' => 25,
                'mu' => 2832,
                'pmu' => 0,
                'excl_wt' => 12,
                'excl_cpu' => 12,
                'excl_mu' => 1040,
                'excl_pmu' => 0
            ),
            'strlen' => array(
                'ct' => 5,
                'wt' => 20,
                'cpu' => 25,
                'mu' => 752,
                'pmu' => 0,
                'excl_wt' => 20,
                'excl_cpu' => 25,
                'excl_mu' => 752,
                'excl_pmu' => 0
            ),
            'bar@3' => array(
                'ct' => 2,
                'wt' => 10,
                'cpu' => 13,
                'mu' => 1792,
                'pmu' => 0,
                'excl_wt' => 8,
                'excl_cpu' => 9,
                'excl_mu' => 1040,
                'excl_pmu' => 0
            ),
            'bar@4' => array(
                'ct' => 1,
                'wt' => 2,
                'cpu' => 4,
                'mu' => 752,
                'pmu' => 0,
                'excl_wt' => 2,
                'excl_cpu' => 4,
                'excl_mu' => 752,
                'excl_pmu' => 0
            ),
            'xhprof_disable' => array(
                'ct' => 1,
                'wt' => 1,
                'cpu' => 2,
                'mu' => 760,
                'pmu' => 0,
                'excl_wt' => 1,
                'excl_cpu' => 2,
                'excl_mu' => 760,
                'excl_pmu' => 0
            )
        );
    }

}
 