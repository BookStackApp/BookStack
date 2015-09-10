<?php

namespace BookStack\Http\Controllers;

use Illuminate\Http\Request;

use BookStack\Http\Requests;
use BookStack\Http\Controllers\Controller;
use BookStack\Repos\BookRepo;
use BookStack\Services\ActivityService;
use BookStack\Services\Facades\Activity;

class HomeController extends Controller
{

    protected $activityService;
    protected $bookRepo;

    /**
     * HomeController constructor.
     * @param ActivityService $activityService
     * @param BookRepo        $bookRepo
     */
    public function __construct(ActivityService $activityService, BookRepo $bookRepo)
    {
        $this->activityService = $activityService;
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
        $books = $this->bookRepo->getAll();
        $activity = $this->activityService->latest();
        return view('home', ['books' => $books, 'activity' => $activity]);
    }

}
