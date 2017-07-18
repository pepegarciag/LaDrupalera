<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Goutte\Client;
use App\Post;

class Bea extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ladrupalera:bea';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch posts for all sections from LaDrupalera';

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
    protected $paths = [
        'drupal/snippet', 
        'drupal/consultoria',
        'drupal/desarrollo',
        'drupal/comunidad-drupal',
    ];

    /**
     * Separator for console outputs.
     * @var string
     */
    protected $separator = "=============================";

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
        foreach ($this->paths as $path) {
            $url = $this->host . $path;
            $this->info($url);
            $this->info($this->separator);

            do {
                $this->fetchPage($url);
            } while($this->hasPages);

            // Reset variables.
            $this->hasPages = TRUE;
            $this->page = 0;
        }
    }

    /**
     * Fetch a snippet page.
     *
     * @return void
     */
    private function fetchPage($url)
    {    
        // Check if we should query for pages
        $query = ($this->page == 0) ? '' : "?page={$this->page}";
        // Perform request.
        $crawler = $this->goutte->request('GET', $url . $query);
        // Fetch all posts.
        $posts = $crawler->filter('h2 > a');

        // Check if we found posts in this request. If not, we assume this page
        // doesn't exists and it is a 404 so we stop requesting posts.
        if ($this->checkPage($posts)) {
            $posts->each(function($node, $i) {
                $this->info($node->text());
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
}
