<?php

namespace  App\Modules\Article\Controllers;

use App\Controllers\BaseController;
use App\Libraries\Settings;
use App\Modules\Article\Models\ArticleModel;

class Article extends BaseController
{
	protected $setting;
	protected $article;

	public function __construct()
	{
		//memanggil Model
		$this->setting = new Settings();
		$this->article = new ArticleModel();
	}

	public function index()
	{
		return view('App\Modules\Article\Views/article', [
			'title' => 'Articles',
		]);
	}

	public function article()
	{
		$uri = new \CodeIgniter\HTTP\URI(current_url());
		$slug = $uri->getSegment(4);
		$article = $this->article->showarticle($slug);
		if (session()->get('lang') == 'id') {
			$articleTitle = $article['article_title'];
		} elseif (session()->get('lang') == 'en') {
			$articleTitle = $article['article_title_en'];
		} else {
			$articleTitle = $article['article_title'];
		}
		return view('article', [
			'title' => ucfirst($articleTitle),
			'slug' => $slug
		]);
	}

}
