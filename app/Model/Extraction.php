<?php

/**
 * The Extractions Model
 */

App::uses('AppModel', 'Model');

class Extraction extends AppModel
{
    /**
     * Set hasMany relationships
     *
     * @var type
     */
    public $hasMany = array(
        'ExtractionDetail'
    );
}
