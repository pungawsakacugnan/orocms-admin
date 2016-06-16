<?php
namespace OroCMS\Admin\Entities;

use Countable;
use Illuminate\Support\Collection;

class LogReader implements Countable
{
    protected $pattern      = '/\[(?P<date>.*)\] (?P<channel>\w+).(?P<level>\w+): (?P<message>.*[^ ]+)/';
    protected $collection   = null;

    /**
     * Constructor
     */
    function __construct($pattern=null)
    {
        if ($pattern) {
            $this->pattern  = $pattern;
        }

        // initialise our collection
        $this->collection   = collect([]);

        // parse logs
        $this->parse();
    }

    /**
     * Get data
     *
     * @return Collection
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * Get available log files
     *
     * @param $basename
     *
     * @return array
     */
    public function getFiles($basename=false)
    {
        $files  = glob(storage_path() . '/logs/*');

        if ($basename && is_array($files)) {
            foreach ($files as &$file) {
                $file   = basename($file);
            }
        }

        // sort
        sort($files);

        return $files;
    }

    /**
     * Paginate
     *
     * @param $offset
     * @param $limit
     *
     * @return mixed
     */
    public function forPage($offset, $limit)
    {
        return $this->collection->forPage($offset, $limit);
    }

    /**
     * Get everything, latest first
     *
     * @return array
     */
    public function all()
    {
        $items  = $this->collection
            ->sortByDesc('date')
            ->all();

        return array_values($items);
    }

    /**
     * Get item count
     *
     * @return integer
     */
    public function count()
    {
        return $this->collection->count();
    }

    /**
     * Get parsed log
     *
     * @param $all
     */
    private function parse($all=false)
    {
        // Get log files
        $files      = $this->getFiles();

        $log_files  = $files;
        $log_data   = [];

        if (empty($log_files)) {
            return false;
        }

        if (!$all) {
            $log_files  = [$log_files[0]];
        }

        // iterate
        foreach ($log_files as $file) {
            static $stack_info  = null,
                $stack_ref  = null,
                $can_stack  = false;

            // load file
            $fp = new \SplFileObject($file, "r");
            while (!$fp->eof()) {
                // get current line
                $line   = $fp->current();

                if (preg_match($this->pattern, $line, $data)) {
                    if (isset($data['date'])) {
                        $message    = $data['message'];
                        $extra      = null;

                        // get extra information from message
                        if (preg_match('/{(.*?)}/', $message, $m)) {
                            $message    = preg_replace("|{$m[0]}|", '', $message);
                            $extra      = json_decode($m[0]);
                        }

                        // add stack
                        if ($data['level'] == 'ERROR') {
                            $can_stack  = true;
                            $stack_info = [];
                        }

                        // set reference
                        $this->collection->push([
                            'date'      => $data['date'],
                            'channel'   => $data['channel'],
                            'level'     => $data['level'],
                            'message'   => $message,
                            'extra'     => $extra
                        ]);
                    }
                }

                // end of stack trace
                if (preg_match('/({main}|\[\])/', $line, $m)) {
                    /*
                     * To update the last added collection item,
                     * we have to remove it, set the changes, and add up again
                     */
                    array_shift($stack_info); // exclude message
                    if (!empty($stack_info)) {
                        $log_item   = $this->collection->pop();
                        if ($log_item) {
                            array_shift($stack_info); // extra line-break
                            $log_item['stack_trace']   = implode('<br>', $stack_info);

                            // well, update it!
                            $this->collection->push($log_item);
                        }
                    }

                    // reset
                    $can_stack  = false;
                    $stack_info = [];
                }

                // collect stack traces
                if ($can_stack) {
                    $stack_info[]   = preg_replace('/Stack trace\:/', '', $line);
                }

                // read next
                $fp->next();
            }
        } // while
    }
}