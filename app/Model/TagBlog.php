<?php

/**
 * The TagBlog Model
 */

App::uses('AppModel', 'Model');

class TagBlog extends AppModel
{
    /**
     * The table to use from the DB
     *
     * @access public
     * @var string
     */
    public $useTable = 'tags_blogs';

}
