<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Goutte\Client;
use App\Post;

class LaDrupalera extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ladrupalera:posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch posts from LaDrupalera';

    /**
     * The client for Goutte Crawler.
     *
     * @var Client
     */
    protected $goutte;

    /**
     * Indicate if we can fetch more pages
     * @var boolean
     */
    protected $hasPages = TRUE;

    /**
     * Current page for fetch posts.
     * @var int
     */
    protected $page = 0;

    /**
     * Host to perform requests.
     * @var string
     */
    protected $host = 'https://www.ladrupalera.com/';

    /**
     * Path to perform requests.
     * @var string
     */
    protected $path = 'drupal/snippet';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Client $goutte)
    {
        parent::__construct();
        $this->goutte = $goutte;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        do {
            $this->fetchPage();
        } while($this->hasPages);
    }

    /**
     * Fetch a snippet page.
     *
     * @return void
     */
    private function fetchPage()
    {
        // Build full URL.
        $url = $this->host . $this->path;
        // Check if we should query for pages
        $query = ($this->page == 0) ? '' : "?page={$this->page}";

        $crawler = $this->goutte->request('GET', $url . $query);

        // Fetch images for each post.
        $images = $crawler->filter('header article > div > img');
        $imgs = $images->each(function($node) {
            return $node->image()->getUri();
        });

        // Fetch all posts.
        $posts = $crawler->filter('h2 > a');
        // Check if we found posts in this request. If not, we assume this page
        // doesn't exists and it is a 404 so we stop requesting posts.
        if ($this->checkPage($posts)) {
            $posts->each(function($node, $i) use ($imgs) {
                // Check if we already have this post on DB.
                $post = Post::where('url', $node->link()->getUri())->get();
                if ($post->isEmpty()) {
                    $image = isset($imgs[$i]) ? $imgs[$i] : '';
                    $this->save($node, $image);
                }
            });

            $this->page++;
        }
        else {
            $this->hasPages = FALSE;
        }
    }

    /**
     * Check if the given posts have any of them.
     *
     * @param  Array $posts
     *
     * @return boolean
     */
    private function checkPage($posts)
    {
        return (count($posts)) ? TRUE : FAlSE;
    }

    /**
     * Save a given post.
     *
     * @param DOMElement $node
     * @param string image
     *
     * @return App\Post
     */
    private function save($node, $image)
    {
        $post = new Post();
        $post->title = $node->text();
        $post->url = $node->link()->getUri();
        $post->image = $image;
        $post->new = TRUE;
        $post->save();

        return $post;
    }
}
