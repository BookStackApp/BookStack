<?php

namespace Oxbow\Http\Controllers;

use Illuminate\Http\Request;

use Oxbow\Http\Requests;
use Oxbow\Http\Controllers\Controller;
use Oxbow\Repos\BookRepo;
use Oxbow\Services\ActivityService;
use Oxbow\Services\Facades\Activity;

class HomeController extends Controller
{

    protected $activityService;
    protected $bookRepo;

    /**
     * HomeController constructor.
     * @param ActivityService $activityService
     * @param BookRepo $bookRepo
     */
    public function __construct(ActivityService $activityService, BookRepo $bookRepo)
    {
        $this->activityService = $activityService;
        $this->bookRepo = $bookRepo;
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
