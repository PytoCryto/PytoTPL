<?php

namespace PytoTPL\Traits;

trait SectionsAwareTrait
{
    /**
     * The list of sections
     * 
     * @var array
     */
    protected $sections = [];

    /**
     * Register a new template section
     * 
     * @param  string $section 
     * @param  mixed  $contents 
     * @param  bool   $overwrite 
     * @return $this
     */
    protected function addSection($section, $contents, $overwrite = false)
    {
        if ($overwrite || !isset($this->sections[$section])) {
            $this->sections[$section] = $contents;
        }

        return $this;
    }

    /**
     * Get the specified template section
     * 
     * @param  string $section 
     * @return mixed
     */
    protected function getSection($section)
    {
        return isset($this->sections[$section])
            ? $this->sections[$section]
            : null;
    }

    /**
     * Get the list of all sections
     * 
     * @return array
     */
    protected function getSections()
    {
        return $this->sections;
    }

    /**
     * Flush all sections
     * 
     * @return $this
     */
    protected function flushSections()
    {
        $this->sections = [];
    }
}
