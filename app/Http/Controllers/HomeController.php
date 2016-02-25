<?php

namespace BookStack\Http\Controllers;

use Activity;
use BookStack\Repos\EntityRepo;
use BookStack\Http\Requests;
use Views;

class HomeController extends Controller
{
    protected $entityRepo;

    /**
     * HomeController constructor.
     * @param EntityRepo $entityRepo
     */
    public function __construct(EntityRepo $entityRepo)
    {
        $this->entityRepo = $entityRepo;
        parent::__construct();
    }


    /**
     * Display the homepage.
     *
     * @return Response
     */
    public function index()
    {
        $activity = Activity::latest(10);
        $recents = $this->signedIn ? Views::getUserRecentlyViewed(12, 0) : $this->entityRepo->getRecentlyCreatedBooks(10);
        $recentlyCreatedPages = $this->entityRepo->getRecentlyCreatedPages(5);
        $recentlyUpdatedPages = $this->entityRepo->getRecentlyUpdatedPages(5);
        return view('home', [
            'activity' => $activity,
            'recents' => $recents,
            'recentlyCreatedPages' => $recentlyCreatedPages,
            'recentlyUpdatedPages' => $recentlyUpdatedPages
        ]);
    }

}
