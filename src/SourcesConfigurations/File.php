<?php
declare(strict_types=1);

namespace Cart\SourcesConfigurations;

use Cart\Contracts\SourceConfiguration;
use Mockery\Exception;

class File implements SourceConfiguration
{
    protected $name = 'cart';

    public function __construct(string $path = '')
    {
        if (file_exists($path)) {
            $this->pathToFile = $path;
        } else {
            throw new Exception('Local file name is not exist.');
        }
    }
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
     * Get name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}