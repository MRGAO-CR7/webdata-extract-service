<?php

/**
 * Description of ExtractionsController
 */
App::uses('InstagramExtractor', 'Lib/WebDataExtractors');
App::uses('AbstractExtractor', 'Lib/WebDataExtractors');

class ExtractionsController extends AppController
{
    public function run()
    {
        if (!$this->request->is('post')) {
            throw new BadRequestException('The wrong HTTP method was used in requesting this endpoint.');
        }
        if (empty($this->request->data['keyword'])) {
            throw new BadRequestException('Cannot process request. The `keyword` is missing from the request.');
        }
        $this->request->data['keyword'] = urlencode($this->request->data['keyword']);
        $this->request->data['website'] = 'https://www.instagram.com/';


        $this->Extraction->set($this->request->data);
        if ($extraction = $this->Extraction->save()) {
            $this->request->data['extraction_id'] = $extraction['Extraction']['id'];
            $extractor = new InstagramExtractor([]);
            $count = $extractor->run('queue', $this->request->data);

            $this->set('status', 'success');
            $this->set('code', '200');
            $this->set('extraction_id', $extraction['Extraction']['id']);
            $this->set('website', $extraction['Extraction']['website']);
            $this->set('keyword', urldecode($extraction['Extraction']['keyword']));
            $this->set('count', $count);

            $this->set('_serialize', array(
                'status', 'code', 'extraction_id', 'website', 'keyword', 'count'
            ));
        } else {
            $this->set('status', 'failed');
            $this->set('code', '400');
            $this->set('_serialize', array('status', 'code'));
        }
    }

    public function extract()
    {
        if (!$this->request->is('post') || empty($this->request->data)) {
            throw new BadRequestException('Missing request body');
        }
        if (empty($this->request->data['website'])) {
            throw new BadRequestException('Cannot process request. The `website` is missing from the request.');
        }
        if (empty($this->request->data['link'])) {
            throw new BadRequestException('Cannot process request. The `link` is missing from the request.');
        }
        if (empty($this->request->data['extraction_id'])) {
            throw new BadRequestException('Cannot process request. The `extraction_id` is missing from the request.');
        }

        $extractor = new InstagramExtractor([]);
        $extractor->run('extract', $this->request->data);

        $this->set('status', 'success');
        $this->set('code', '200');
        $this->set('_serialize', array('status', 'code'));
    }
}
