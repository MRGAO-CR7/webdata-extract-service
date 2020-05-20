<?php

App::uses('AbstractExtractor', 'Lib/WebDataExtractors');

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

class InstagramExtractor extends AbstractExtractor
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function queue($options)
    {
        $this->driver->get('https://www.instagram.com/explore/tags/' . $options['tag']);

        $this->waitUntil(10, WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
            WebDriverBy::cssSelector("footer._8Rna9")
        ));

        $count = 0;
        $labels = [];
        do {
            $this->driver->executeScript("window.scrollTo(0, (document.body.scrollHeight))");
            $count += 1;

            $htmlString = $this->driver->getPageSource();
            $preg = '/<a .*?href="\/p\/(.*?)\/".*?>/is';
            preg_match_all($preg, $htmlString, $match);

            foreach ($match[1] as $label) {
                $labels[$label] = $label;
            }
        } while ($count < 350);

        unset($options['tag']);

        $options['labels'] = $labels;
        App::uses('RabbitMQ', 'Lib');
        $RabbitMQ = new RabbitMQ;
        $RabbitMQ->setQueue('labels_extract');
        $channel = $RabbitMQ->getChannel($RabbitMQ->getConnection());
        $RabbitMQ->publishMessage($channel, $options);

        return count($labels);
    }

    protected function extract($options)
    {
        $this->driver->get($options['website'] . 'p/' . $options['label']);
        $this->waitUntil(10, WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
            WebDriverBy::cssSelector("a.sqdOP")
        ));

        $element = $this->driver->findElement(WebDriverBy::cssSelector("a.sqdOP"));
        $options['name'] = $element->getText();
        $options['url_link'] = 'https://www.instagram.com/' . $options['name'];

        $this->driver->get($options['url_link']);
        $this->waitUntil(10, WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
            WebDriverBy::cssSelector("footer._8Rna9")
        ));

        $spanElements = $this->driver->findElements(WebDriverBy::cssSelector("span.g47SY"));
        $options['posts'] = $spanElements[0]->getText();
        $options['followers'] = $spanElements[1]->getText();
        $options['following'] = $spanElements[2]->getText();

        App::uses('Blog', 'Model');
        $blog = new Blog();
        $options['blog_id'] = $blog->addOrUpdate($options);

        $this->saveExtractionDetail($options);
        $this->saveTagBlog($options);

        return true;
    }

    private function saveExtractionDetail($options)
    {
        App::uses('ExtractionDetail', 'Model');
        $extractionDetail = new ExtractionDetail();

        try {
            $extractionDetail->save($options);
        } catch (Exception $e) {
        }
    }

    private function saveTagBlog($options)
    {
        App::uses('TagBlog', 'Model');
        $tagBlog = new TagBlog();

        try {
            $tagBlog->save($options);
        } catch (Exception $e) {
        }
    }
}
