<?php

namespace BookStack\Http\Controllers;

use Activity;
use Illuminate\Http\Request;

use BookStack\Http\Requests;
use BookStack\Repos\BookRepo;
use Views;

class HomeController extends Controller
{

    protected $activityService;
    protected $bookRepo;

    /**
     * HomeController constructor.
     * @param BookRepo        $bookRepo
     */
    public function __construct(BookRepo $bookRepo)
    {
        $this->bookRepo = $bookRepo;
        parent::__construct();
    }


    /**
     * Display the homepage.
     *
     * @return Response
     */
    public function index()
    {
        $activity = Activity::latest();
        $recentlyViewed = Views::getUserRecentlyViewed(10, 0);
        return view('home', ['activity' => $activity, 'recents' => $recentlyViewed]);
    }

}
