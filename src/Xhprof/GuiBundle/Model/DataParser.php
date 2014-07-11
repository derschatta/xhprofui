<?php

namespace Xhprof\GuiBundle\Model;

use \InvalidArgumentException;

class DataParser
{

    const SORT_ASC  = 'ASC';
    const SORT_DESC = 'DESC';

    const METRIC_COUNT = 'ct';
    const METRIC_WALL_TIME = 'wt';
    const METRIC_CPU = 'cpu';
    const METRIC_MEMORY_USAGE = 'mu';
    const METRIC_PEAK_MEMORY_USAGE = 'pmu';

    /**
     * all available metric keys
     *
     * @var array
     */
    private $metrics = [
        self::METRIC_COUNT,
        self::METRIC_WALL_TIME,
        self::METRIC_CPU,
        self::METRIC_MEMORY_USAGE,
        self::METRIC_PEAK_MEMORY_USAGE
    ];

    /**
     * parse and returns the data sorted by the given metric and direction
     *
     * @param array  $raw_data
     * @param string $sort_by_metric
     * @param string $sort_direction
     *
     * @throws \InvalidArgumentException
     * @return array
     */
    public function parse(array $raw_data, $sort_by_metric = null, $sort_direction = null)
    {
        if ($sort_by_metric === null) {
            $sort_by_metric = self::METRIC_WALL_TIME;
        }
        if ($sort_direction === null) {
            $sort_direction = self::SORT_DESC;
        }
        $sort_by_metric = strtolower($sort_by_metric);
        $sort_direction = strtoupper($sort_direction);

        if (!in_array($sort_by_metric, $this->metrics)) {
            throw new InvalidArgumentException('unknown metric for sorting given!');
        }
        if ($sort_direction != self::SORT_DESC && $sort_direction != self::SORT_ASC) {
            throw new InvalidArgumentException('unknown sorting direction given!');
        }

        $parsed_data = [];
        foreach ($raw_data as $function_name => $row) {
            list($parent, $child) = $this->splitFunctionName($function_name);

            if (!isset($parsed_data[$child])) {
                $parsed_data[$child] = array(self::METRIC_COUNT => $row[self::METRIC_COUNT]);
                foreach ($this->metrics as $metric) {
                    $parsed_data[$child][$metric] = $row[$metric];
                }
            } else {
                /* increment call count for this child */
                $parsed_data[$child][self::METRIC_COUNT] += $row[self::METRIC_COUNT];

                /* update inclusive times/metric for this child  */
                foreach ($this->metrics as $metric) {
                    $parsed_data[$child][$metric] += $row[$metric];
                }
            }

        }

        return $this->sortDataByMetric($parsed_data, $sort_by_metric, $sort_direction);
    }

    /**
     * splits the function name into parent and child parts
     *
     * @param string $function
     *
     * @return array
     */
    private function splitFunctionName($function)
    {
        if (strpos($function, '==>') === false) {
            return [null, $function];
        }

        return explode("==>", $function);
    }

    /**
     * sort by given metric and direction
     *
     * @param array  $data
     * @param string $metric
     * @param string $direction sort direction (ASC/DESC), defaults to ASC
     *
     * @return array
     */
    private function sortDataByMetric($data, $metric, $direction = self::SORT_ASC)
    {
        uasort(
            $data,
            function ($a, $b) use ($metric, $direction) {
                if ($a[$metric] == $b[$metric]) {
                    return 0;
                }

                if ($direction == self::SORT_DESC) {
                    return ($a[$metric] < $b[$metric]) ? 1 : -1;
                } else {
                    return ($a[$metric] < $b[$metric]) ? -1 : 1;
                }
            }
        );

        return $data;
    }

} 