<?php

namespace App\Http\Controllers;

use Goutte\Client;
use App\Post;

class LaDrupaleraController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function posts()
    {
        return response()->json(Post::all());
    }

    public function ladrupalera()
    {
        return view('ladrupalera.index');
    }
}
