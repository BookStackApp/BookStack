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
     * @return Response
     */
    public function index()
    {
        $activity = Activity::latest(10);
        $draftPages = $this->signedIn ? $this->entityRepo->getUserDraftPages(6) : [];
        $recentFactor = count($draftPages) > 0 ? 0.5 : 1;
        $recents = $this->signedIn ? Views::getUserRecentlyViewed(12*$recentFactor, 0) : $this->entityRepo->getRecentlyCreatedBooks(10*$recentFactor);
        $recentlyCreatedPages = $this->entityRepo->getRecentlyCreatedPages(5);
        $recentlyUpdatedPages = $this->entityRepo->getRecentlyUpdatedPages(5);
        return view('home', [
            'activity' => $activity,
            'recents' => $recents,
            'recentlyCreatedPages' => $recentlyCreatedPages,
            'recentlyUpdatedPages' => $recentlyUpdatedPages,
            'draftPages' => $draftPages
        ]);
    }

}
