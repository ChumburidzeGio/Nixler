<?php

namespace Modules\Dashboard\Services;

use Illuminate\Foundation\Application;
use Psr\Log\LogLevel;
use ReflectionClass;
use Carbon\Carbon;

class LogsService
{

    private static $levels = [
        'debug',
        'info',
        'notice',
        'warning',
        'error',
        'critical',
        'alert',
        'emergency',
    ];


    const MAX_FILE_SIZE = 52428800;


	/**
     * Read log file data and return as array
     *
     * @return null|array
     */
	public function read($limit = 10){

        $log = array();

        $log_levels = self::getLogLevels();

        $pattern = '/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\].*/';

        $path = $this->findNewestLocalLogfile();

        if (app('files')->size($path) > self::MAX_FILE_SIZE) return null;

        $file = app('files')->get($path);

        preg_match_all($pattern, $file, $headings);

        if (!is_array($headings)) return $log;

        $log_data = preg_split($pattern, $file);

        if ($log_data[0] < 1) {
            array_shift($log_data);
        }

        foreach ($headings as $h) {

            foreach (array_reverse($h) as $i => $item) {

                foreach ($log_levels as $level_key => $level_value) {

                    if (strpos(strtolower($item), '.' . $level_value) && $i < $limit) {

                        preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\].*?(\w+)\.' . $level_key . ': (.*?)( in .*?:[0-9]+)?$/', $item, $current);

                        if (!isset($current[3])) continue;

                        $log[] = array(
                            'context' => $current[2],
                            'level' => $level_value,
                            'date' => $current[1],
                            'ago' => Carbon::createFromTimeStamp(strtotime($current[1]))->diffForHumans(),
                            'text' => $current[3],
                            'in_file' => isset($current[4]) ? $current[4] : null,
                            'stack' => preg_replace("/^\n*/", '', $log_data[$i])
                        );
                    }

                }
            }
        }

        return $log;

	}


	/**
     * Get the path to the latest local Laravel log file.
     *
     * @return null|string
     */
    protected function findNewestLocalLogfile()
    {
        $files = glob(storage_path('logs').'/*.log');

        $files = array_combine($files, array_map('filemtime', $files));

        arsort($files);

        $newestLogFile = key($files);

        return $newestLogFile;
    }


	/**
     * Get the path to the latest local Laravel log file.
     *
     * @return null|string
     */
    private static function getLogLevels()
    {
        $class = new ReflectionClass(new LogLevel);
        return $class->getConstants();
    }

}