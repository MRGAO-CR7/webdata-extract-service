<?php

/**
 * Description of ExtractionsController
 */
App::uses('InstagramExtractor', 'Lib/WebDataExtractors');
App::uses('AbstractExtractor', 'Lib/WebDataExtractors');
App::uses('Tag', 'Model');
App::uses('Label', 'Model');
App::uses('TagLabel', 'Model');
App::uses('ExtractionDetail', 'Model');
App::uses('RabbitMQ', 'Lib');

class ExtractionsController extends AppController
{
    public function run()
    {
        if (!$this->request->is('post')) {
            throw new BadRequestException('The wrong HTTP method was used in requesting this endpoint.');
        }

        if (empty($this->request->data['tag'])) {
            throw new BadRequestException('Cannot process request. The `tag` is missing from the request.');
        }

        if (empty($this->request->data['height'])) {
            $this->request->data['height'] = 0;
        }

        $this->request->data['website'] = 'https://www.instagram.com/';
        $this->request->data['created'] = date('Y-m-d H:i:s');
        $options = $this->request->data;
        $this->request->data['tag'] = urlencode($this->request->data['tag']);

        if ($extraction = $this->Extraction->save($this->request->data)) {
            $options['extraction_id'] = $extraction['Extraction']['id'];
            $options['tag'] = urlencode($this->request->data['tag']);

            $tagId = $this->findOrCreateTag($options);
            $options['tag_id'] = $tagId;

            $extractor = new InstagramExtractor();
            $extraction['Extraction']['height'] = $extractor->run('queue', $options);
            $this->Extraction->save($extraction['Extraction']);

            $this->set('status', 'success');
            $this->set('code', '200');
            $this->set('extraction_id', $extraction['Extraction']['id']);
            $this->set('website', $extraction['Extraction']['website']);
            $this->set('tag', $this->request->data['tag']);
            $this->set('tag_encoded', $options['tag']);
            $this->set('height', $extraction['Extraction']['height']);

            $this->set('_serialize', array(
                'status', 'code', 'extraction_id', 'website', 'tag', 'tag_encoded', 'height'
            ));
        } else {
            $this->set('status', 'failed');
            $this->set('code', '400');
            $this->set('_serialize', array('status', 'code'));
        }
    }

    public function labels_extract()
    {
        $options = $this->request->data;
        $labelId = $this->findOrCreateLabel($options);
        $options['label_id'] = $labelId;

        $this->saveTagLabel($options);

        $this->loadModel('ExtractionDetail');
        $this->ExtractionDetail->save($options);


        $this->set('status', 'success');
        $this->set('code', '200');
        $this->set('description', 'ran the labels_extract queue successfully.');
        $this->set('_serialize', array('status', 'code', 'description'));
    }

    public function sub_queue()
    {
        $options = $this->request->data;
        $labels = $options['labels'];
        unset($options['labels']);

        foreach ($labels as $label) {
            $options['label'] = $label;

            App::uses('RabbitMQ', 'Lib');
            $RabbitMQ = new RabbitMQ;
            $RabbitMQ->setQueue('data_extract');
            $channel = $RabbitMQ->getChannel($RabbitMQ->getConnection());
            $RabbitMQ->publishMessage($channel, $options);
        }

        $this->set('status', 'success');
        $this->set('code', '200');
        $this->set('description', 'ran the sub queue successfully.');
        $this->set('_serialize', array('status', 'code', 'description'));
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
    }

    private function findOrCreateLabel($options)
    {
        $this->loadModel('Label');
        if ($result = $this->Label->findByName($options['label'])) {
            return $result['Label']['id'];
        }

        $this->Label->create();
        $data['name'] = $options['label'];
        $data['created'] = $options['created'];
        if ($result = $this->Label->save($data)) {
            return $result['Label']['id'];
        }
    }

    private function saveTagLabel($options)
    {
        $this->loadModel('TagLabel');
        try {
            $this->TagLabel->save($options);
        } catch (Exception $e) {
        }
    }
}
