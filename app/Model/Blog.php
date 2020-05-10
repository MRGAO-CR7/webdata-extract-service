<?php

/**
 * The Blogs Model
 */

App::uses('AppModel', 'Model');

class Blog extends AppModel
{
    public function addOrUpdate($options)
    {
        if ($result = $this->findByName($options['name'])) {
            if ($result['Blog']['posts'] <> $options['posts']
                || $result['Blog']['followers'] <> $options['followers']
                || $result['Blog']['following'] <> $options['following']) {
                $result['Blog']['posts'] = $options['posts'];
                $result['Blog']['followers'] = $options['followers'];
                $result['Blog']['following'] = $options['following'];
                $result['Blog']['updated'] = date('Y-m-d H:i:s');

                $this->save($result['Blog']);
            }

            return $result['Blog']['id'];
        }

        $this->create();
        if ($result = $this->save($options)) {
            return $result['Blog']['id'];
        }

        throw new Exception("Adding or updating a blog failed.");
    }
}
