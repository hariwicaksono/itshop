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
		$slug = $uri->getSegment(3);
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

		// Build SEO description from headline (strip HTML tags)
		$articleHeadline = '';
		if (session()->get('lang') == 'en') {
			$articleHeadline = $article['article_headline_en'] ?: $article['article_headline'];
		} else {
			$articleHeadline = $article['article_headline'];
		}
		$seoDescription = $articleHeadline ?: strip_tags($article['article_body_en'] ?: $article['article_body']);
		$seoDescription = substr(strip_tags($seoDescription), 0, 160);

		// Determine article image
		$articleImage = $article['media_path'] ?? null;

		// Author name
		$authorName = trim(($article['first_name'] ?? '') . ' ' . ($article['last_name'] ?? ''));

		// Publish date
		$publishDate = $article['created_at'] ?? date('Y-m-d H:i:s');

		// Category
		$categoryName = $article['category_name'] ?? '';

		// Construct image URL for OG
		$imgLogo = $this->setting->info['img_logo'];
		$articleImageUrl = $articleImage ? base_url() . $articleImage : base_url('images/') . $imgLogo;

		return view('article', [
			'title' => ucfirst($articleTitle),
			'slug' => $slug,
			'article' => $article,
			'article_headline' => $articleHeadline,
			'article_image' => $articleImage,
			'article_image_url' => $articleImageUrl,
			'article_description' => $seoDescription,
			'article_author' => $authorName,
			'article_category' => $categoryName,
			'article_publish_date' => $publishDate,
			'article_update_date' => $article['updated_at'] ?? $publishDate,
		]);
	}
}
