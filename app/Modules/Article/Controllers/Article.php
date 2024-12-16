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
		helper('cookie');
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

		//set cookie for today
        $awal  = new \DateTime(date('Y-m-d 23:59:59'));
        $akhir = new \DateTime(); // Waktu sekarang
        $diff  = $awal->diff($akhir);
        $jam = $diff->h;
        $detik = $jam * 3600;
        $time = time() + $detik;
        if (!get_cookie("itshop_article_view_id_" . $article['article_id'])) {
            setcookie("itshop_article_view_id_" . $article['article_id'], true, $time, "/", null, null, true);
			$this->article->update($article['article_id'], [
				"views" => $article['views'] + 1
			]);
        }

		return view('article', [
			'title' => ucfirst($articleTitle),
			'slug' => $slug
		]);
	}

}
