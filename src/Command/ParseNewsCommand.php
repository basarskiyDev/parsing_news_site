<?php

namespace App\Command;

use App\Message\ParseNewsMessage;
use App\Message\ParseNewsPageMessage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'parse_news',
    description: 'Add news to rabbitmq queue',
)]
class ParseNewsCommand extends Command
{
    protected static $defaultName = 'parse_news';

    public function __construct(
        private HttpClientInterface $httpClient,
        private MessageBusInterface $messageBus
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        foreach ($this->addToQueue() as $content) {
            $this->messageBus->dispatch(new ParseNewsPageMessage($content));
        }


        return Command::SUCCESS;


//        $response = $this->httpClient->request('POST',
//            'https://highload.today/wp-content/themes/supermc/ajax/loadarchive.php', [
//                'body' => [
//                    'action' => 'archiveload',
//                    'stick' => 35,
//                    'page' => 100,
//                    'cat' => 537
//                ]
//            ]);
//
//        $content = $response->getContent();
//        dd($content);
//
//        $crawler = new Crawler($content);
//
//        $crawler->filter('.lenta-item')->each(
//            function (Crawler $node, $i) {
//            $this->messageBus->dispatch(new ParseNewsMessage($node));
//            }
//        );

    }

    private function addToQueue()
    {
        $page = 0;
        $content = true;
        while ($content) {

            $response = $this->httpClient->request('POST',
                'https://highload.today/wp-content/themes/supermc/ajax/loadarchive.php', [
                    'body' => [
                        'action' => 'archiveload',
                        'stick' => 35,
                        'page' => $page,
                        'cat' => 537
                    ]
                ]);

            $content = $response->getContent();
            $page++;
            if ($content){
                yield $response->getContent()??$response->getContent();
            }
        }
    }
}
