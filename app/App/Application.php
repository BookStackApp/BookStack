<?php

namespace BookStack\App;

class Application extends \Illuminate\Foundation\Application
{
    /**
     * Get the path to the application configuration files.
     *
     * @param string $path Optionally, a path to append to the config path
     *
     * @return string
     */
    public function configPath($path = '')
    {
        return $this->basePath
            . DIRECTORY_SEPARATOR
            . 'app'
            . DIRECTORY_SEPARATOR
            . 'Config'
            . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}
