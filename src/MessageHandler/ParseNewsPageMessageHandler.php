<?php

namespace App\MessageHandler;

use App\Message\ParseNewsMessage;
use App\Message\ParseNewsPageMessage;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class ParseNewsPageMessageHandler implements MessageHandlerInterface
{
    public function __construct(
        private MessageBusInterface $messageBus)
    {
    }

    public function __invoke(ParseNewsPageMessage $message)
    {
        $crawler = new Crawler($message->getPage());

        $crawler->filter('.lenta-item')->each(
            function (Crawler $node, $i) {
                $this->messageBus->dispatch(new ParseNewsMessage($node->html()));
            }
        );
    }
}
