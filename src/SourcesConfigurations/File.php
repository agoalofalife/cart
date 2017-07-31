<?php
declare(strict_types=1);

namespace Cart\SourcesConfigurations;

use Cart\Contracts\SourceConfiguration;
use Mockery\Exception;

class File implements SourceConfiguration
{
    /**
     * @var string
     */
    protected $pathToFile;

    /**
     * get array with configuration
     * @return array
     */

    public function get() : array
    {
        $configuration =  require $this->pathToFile;
        return $configuration;
    }
    /**
     * @param $path string  path to file
     * @return bool
     */
    public function setSource($path) : bool
    {
        if (file_exists($path)) {
            $this->pathToFile = $path;
        } else {
            throw new Exception('Local file name is not exist.');
        }
        return true;
    }
}