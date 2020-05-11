<?php

/**
 * Description of ExtractionsController
 */
App::uses('InstagramExtractor', 'Lib/WebDataExtractors');
App::uses('AbstractExtractor', 'Lib/WebDataExtractors');
App::uses('Tag', 'Model');
App::uses('RabbitMQ', 'Lib');

class ExtractionsController extends AppController
{
    public function run()
    {
        if (!$this->request->is('post')) {
            throw new BadRequestException('The wrong HTTP method was used in requesting this endpoint.');
        }

        if (empty($this->request->data['keywords'])) {
            throw new BadRequestException('Cannot process request. The `keywords` is missing from the request.');
        }

        $this->request->data['website'] = 'https://www.instagram.com/';
        $this->request->data['created'] = date('Y-m-d H:i:s');
        $options = $this->request->data;
        $this->request->data['keywords'] = urlencode($this->request->data['keywords']);

        if ($extraction = $this->Extraction->save($this->request->data)) {
            $tags = explode(",", $options['keywords']);

            $data = [];
            $idx = 0;
            foreach ($tags as $tag) {
                $options['extraction_id'] = $extraction['Extraction']['id'];
                $options['tag'] = urlencode($tag);

                $tagId = $this->findOrCreateTag($options);
                $options['tag_id'] = $tagId;
                unset($options['keywords']);

                $RabbitMQ = new RabbitMQ;
                $RabbitMQ->setQueue('labels_extract');
                $channel = $RabbitMQ->getChannel($RabbitMQ->getConnection());
                $RabbitMQ->publishMessage($channel, $options);

                $data[$idx]['tag'] = $tag;
                $data[$idx]['tag_encoded'] = $options['tag'];
                $idx++;
            }

            $this->set('status', 'success');
            $this->set('code', '200');
            $this->set('extraction_id', $extraction['Extraction']['id']);
            $this->set('website', $extraction['Extraction']['website']);
            $this->set('data', $data);

            $this->set('_serialize', array(
                'status', 'code', 'extraction_id', 'website', 'data'
            ));
        } else {
            $this->set('status', 'failed');
            $this->set('code', '400');
            $this->set('_serialize', array('status', 'code'));
        }
    }

    public function queue()
    {
        $extractor = new InstagramExtractor();
        $extractor->run('queue', $this->request->data);
    }

    public function extract()
    {
        if (!$this->request->is('post') || empty($this->request->data)) {
            throw new BadRequestException('Missing request body');
        }
        if (empty($this->request->data['website'])) {
            throw new BadRequestException('Cannot process request. The `website` is missing from the request.');
        }
        if (empty($this->request->data['label'])) {
            throw new BadRequestException('Cannot process request. The `label` is missing from the request.');
        }
        if (empty($this->request->data['extraction_id'])) {
            throw new BadRequestException('Cannot process request. The `extraction_id` is missing from the request.');
        }
        if (empty($this->request->data['tag_id'])) {
            throw new BadRequestException('Cannot process request. The `tag_id` is missing from the request.');
        }

        $extractor = new InstagramExtractor();
        $extractor->run('extract', $this->request->data);

        $this->set('status', 'success');
        $this->set('code', '200');
        $this->set('_serialize', array('status', 'code'));
    }

    private function findOrCreateTag($options)
    {
        $this->loadModel('Tag');
        if ($result = $this->Tag->findByName($options['tag'])) {
            return $result['Tag']['id'];
        }

        $this->Tag->create();
        $data['name'] = $options['tag'];
        $data['created'] = $options['created'];
        if ($result = $this->Tag->save($data)) {
            return $result['Tag']['id'];
        }

        throw new Exception('DB Error: Saving a tag failed.');
    }
}
