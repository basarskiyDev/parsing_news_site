<?php

namespace App\MessageHandler;

use App\Entity\Post;
use App\Message\ParseNewsMessage;
use App\Repository\PostRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ParseNewsMessageHandler implements MessageHandlerInterface
{
    private Filesystem $filesystem;

    public function __construct(
        private PostRepository        $postRepository,
        private ManagerRegistry       $managerRegistry,
        private ParameterBagInterface $parameterBag,
        private HttpClientInterface   $httpClient
    )
    {
        $this->filesystem = new Filesystem();
    }

    public function __invoke(ParseNewsMessage $message)
    {
        $crawler = new Crawler($message->getNode());

        $postHeader = $crawler->filter('h2')->text();
        $postDescription = trim($crawler->filter('p')->text());

        $img =  $crawler->filter('img');
        $imgSavePath = null;
        if (count($img)){
            $postImageUrl = $crawler->filter('img')->attr('src');
            $imgSavePath = $this->saveImage($postImageUrl, $postHeader . '.jpg');
        }

        $postDate = $this->getPostDate($crawler);

        $post = $this->postRepository->findOneBy([
            'header' => $postHeader
        ]);

        if (!$post) {
            $post = new Post();
            $post->setHeader($postHeader);
        }else{
            $post->setLastUpdateDate(new \DateTime());
        }

        $post->setDescription($postDescription);
        $post->setAddingDate($postDate);
        $post->setImage($imgSavePath);

        $this->managerRegistry->getManager()->persist($post);
        $this->managerRegistry->getManager()->flush();
    }


    private function saveImage(string $url, string $name)
    {

        $publicFilePath = '/storage/images/' . $name;

        $saveImagePath = $this->parameterBag->get('kernel.project_dir')
            . "/public/storage/images/{$name}";

        $content = file_get_contents($url);

        $fp = fopen($saveImagePath, "w");
        fwrite($fp, $content);
        fclose($fp);

        return $publicFilePath;
    }


    private function getPostDate(Crawler $post)
    {
        $postUrl = $post->filter('h2')->closest('a')->attr('href');

        $response = $this->httpClient->request('GET', $postUrl);

        $crawler = new Crawler($response->getContent());

        $date = $crawler->filter('meta[itemprop="datePublished"]')->attr('content');

        return new \DateTime($date);
    }


}
