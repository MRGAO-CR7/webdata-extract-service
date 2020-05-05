<?php

App::uses('AbstractExtractor', 'Lib/WebDataExtractors');

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

class InstagramExtractor extends AbstractExtractor
{
    public function __construct($options = [])
    {
        parent::__construct();
    }

    protected function queue($options = [])
    {
        $this->driver->get('https://www.instagram.com/explore/tags/' . $options['keyword']);

        $this->waitUntil(10, WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
            WebDriverBy::cssSelector("div._4emnV")
        ));

        $htmlString = $this->driver->getPageSource();
        $preg = '/<a .*?href="\/p\/(.*?)\/".*?>/is';
        preg_match_all($preg, $htmlString, $match);

        foreach ($match[1] as $link) {
            $payload = $options;
            $payload['link'] = $link;

            App::uses('RabbitMQ', 'Lib');
            $RabbitMQ = new RabbitMQ;
            $RabbitMQ->setQueue('data_extract');
            $channel = $RabbitMQ->getChannel($RabbitMQ->getConnection());
            $RabbitMQ->publishMessage($channel, $payload);
        }

        return count($match[1]);
    }

    protected function extract($options = [])
    {
        $this->driver->get($options['website'] . 'p/' . $options['link']);

        $this->waitUntil(10, WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
            WebDriverBy::cssSelector("a.sqdOP")
        ));

        $element = $this->driver->findElement(WebDriverBy::cssSelector("a.sqdOP"));
        $userName = $element->getText();

        $nameLink = 'https://www.instagram.com/' . $userName;
        $this->driver->get($nameLink);

        $this->waitUntil(10, WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
            WebDriverBy::cssSelector("div.Z2m7o")
        ));

        $spanElements = $this->driver->findElements(WebDriverBy::cssSelector("span.g47SY"));

        App::uses('Blog', 'Model');
        $blog = new Blog();
        $blog->save([
            'extraction_id' => $options['extraction_id'],
            'label' => $options['link'],
            'name' => $userName,
            'url_link' => $nameLink,
            'posts' => $spanElements[0]->getText(),
            'followers' => $spanElements[1]->getText(),
            'following' => $spanElements[1]->getText(),
        ]);

        return true;
    }
}
