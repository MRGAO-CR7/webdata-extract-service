<?php

/**
 * The Extractions Model
 */

class Extraction extends AppModel
{
    /**
     * Set hasMany relationships
     *
     * @var type
     */
    public $hasMany = array(
        'Blog'
    );
}
