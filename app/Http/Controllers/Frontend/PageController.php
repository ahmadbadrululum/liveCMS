<?php

namespace App\Http\Controllers\Frontend;

use Carbon\Carbon;
use App\Models\Article;
use App\Models\StaticPage;
use App\liveCMS\Models\Permalink;
use App\liveCMS\Controllers\FrontendController;
use Illuminate\Http\Request;

class PageController extends FrontendController
{
    public function home()
    {
        // if set launching time
        $launchingDateTime = globalParams('launching_datetime') ?
        new Carbon(globalParams('launching_datetime')) : Carbon::now();


        // check if has home permalink
        $permalink = Permalink::withDependencies()->whereIn('permalink', ['/', ''])->first();

        // if home exist or not yet launch
        if ($permalink == null || $launchingDateTime->isFuture()) {
            return redirect('coming-soon');
        }

        $post = $permalink->postable;

        $title = globalParams('home_title', config('livecms.home_title', 'Home'));

        return view(theme('front', 'home'), compact('post', 'title'));
    }

    public function getArticle($slug = null)
    {
        if ($slug == null) {

            $articles = Article::orderBy('published_at', 'DESC')->simplePaginate(1);

            return view(theme('front', (request()->ajax() ? 'partials.articles' : 'articles')), compact('articles'));

        }

        $post = $article = Article::where('slug', $slug)->firstOrFail();

        return view(theme('front', 'article'), compact('post', 'article'));
    }

    public function getStatis($slug = null)
    {
        $post = $statis = StaticPage::where('slug', $slug)->firstOrFail();

        return view(theme('front', 'staticpage'), compact('post', 'statis'));
    }

    public function getByPermalink($permalink)
    {
        $page = Permalink::where('permalink', $permalink)->firstOrFail();

        $type = strtolower(basename($page->portable_type));

        return view(theme('front', $permalink), ['post' => $page->postable]);
    }

    public function routes()
    {
        $parameters = func_get_args();

        $articleSlug = globalParams('slug_article', config('livecms.slugs.article'));

        if ($parameters[0] == $articleSlug) {
            $param = isset($parameters[1]) ? $parameters[1] : null;
            return $this->getArticle($param);
        }


        $statisSlug = globalParams('slug_staticpage', config('livecms.slugs.staticpage'));

        if ($parameters[0] == $statisSlug) {
            return $this->getStatis($parameters[1]);
        }

        $permalink = implode('/', $parameters);

        return $this->getByPermalink($permalink);
    }
}
