<?php namespace BookStack\Http\Controllers;

use Activity;
use BookStack\Repos\EntityRepo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
        $books = $this->entityRepo->getAll('book', false);
        $pages = $this->entityRepo->getAll('page', false);

        $chapters = $this->entityRepo->getAll('chapter', false);
        $links = $this->entityRepo->getAll('link', false);

        // Custom homepage
        $customHomepage = false;
        $homepageSetting = setting('app-homepage');
        if ($homepageSetting) {
            $id = intval(explode(':', $homepageSetting)[0]);
            $customHomepage = $this->entityRepo->getById('page', $id, false, true);
            $this->entityRepo->renderPage($customHomepage, true);
        }

        $view = $customHomepage ? 'home-custom' : 'home';
        return view($view, [
            'books' => $books,
            'pages' => $pages,
            'chapters' => $chapters,
            'links' => $links, 
            'customHomepage' => $customHomepage
        ]);
    }

    /**
     * Get a js representation of the current translations
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function getTranslations()
    {
        $locale = app()->getLocale();
        $cacheKey = 'GLOBAL_TRANSLATIONS_' . $locale;
        if (cache()->has($cacheKey) && config('app.env') !== 'development') {
            $resp = cache($cacheKey);
        } else {
            $translations = [
                // Get only translations which might be used in JS
                'common' => trans('common'),
                'components' => trans('components'),
                'entities' => trans('entities'),
                'errors' => trans('errors')
            ];
            if ($locale !== 'en') {
                $enTrans = [
                    'common' => trans('common', [], 'en'),
                    'components' => trans('components', [], 'en'),
                    'entities' => trans('entities', [], 'en'),
                    'errors' => trans('errors', [], 'en')
                ];
                $translations = array_replace_recursive($enTrans, $translations);
            }
            $resp = 'window.translations = ' . json_encode($translations);
            cache()->put($cacheKey, $resp, 120);
        }

        return response($resp, 200, [
            'Content-Type' => 'application/javascript'
        ]);
    }

    /**
     * Get an icon via image request.
     * Can provide a 'color' parameter with hex value to color the icon.
     * @param $iconName
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getIcon($iconName, Request $request)
    {
        $attrs = [];
        if ($request->filled('color')) {
            $attrs['fill'] = '#' . $request->get('color');
        }

        $icon = icon($iconName, $attrs);
        return response($icon, 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'max-age=3600',
        ]);
    }

    /**
     * Get custom head HTML, Used in ajax calls to show in editor.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function customHeadContent()
    {
        return view('partials/custom-head-content');
    }
}
