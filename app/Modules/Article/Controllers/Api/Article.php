<?php

namespace App\Modules\Article\Controllers\Api;

use App\Controllers\BaseControllerApi;
use App\Modules\Article\Models\ArticleModel;
use Gemini;
use Gemini\Data\Content;
use Gemini\Enums\Role;

class Article extends BaseControllerApi
{
    protected $format       = 'json';
    protected $modelName    = ArticleModel::class;

    public function __construct()
    {
        //memanggil Model
    }

    /**
     * Update the provided string to a slug-safe format.
     *
     * @param string $string
     * @return string
     */
    function slugify($string)
    {
        return strtolower(trim(preg_replace('~[^0-9a-z]+~i', '-', html_entity_decode(preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', htmlentities(preg_replace('/[&]/', ' and ', $string), ENT_QUOTES, 'UTF-8')), ENT_QUOTES, 'UTF-8')), '-'));
    }

    public function index()
    {
        $data = $this->model->getArticles();
        if (!empty($data)) {
            $response = [
                "status" => true,
                "message" => lang('App.getSuccess'),
                "data" => $data
            ];
            return $this->respond($response, 200);
        } else {
            $response = [
                'status' => false,
                'message' => lang('App.noData'),
                'data' => []
            ];
            return $this->respond($response, 200);
        }
    }

    public function allArticles()
    {
        $input = $this->request->getVar();
        $page = $input['page'];
        $limit = $input['limit'];
        $where = $input['category'] ?? "";
        $orderBy = $input['sort_by'];
        $data = $this->model->getAllArticles($page, $limit, $where, $orderBy);
        if (!empty($data)) {
            $response = [
                "status" => true,
                "message" => lang('App.getSuccess'),
                "data" => $data,
                "per_page" => $limit,
                "total_page" => $this->model->countAllResults()
            ];
            return $this->respond($response, 200);
        } else {
            $response = [
                'status' => false,
                'message' => lang('App.noData'),
                'data' => []
            ];
            return $this->respond($response, 200);
        }
    }

    public function show($slug = null)
    {
        $data = $this->model->showArticle($slug);
        if (session()->get('lang') == 'id') {
            $title = $data['article_title'];
            $headline = $data['article_headline'];
            $body = $data['article_body'];
        } elseif (session()->get('lang') == 'en') {
            $title = $data['article_title_en'];
            $headline = $data['article_headline_en'];
            $body = $data['article_body_en'];
        } else {
            $title = $data['article_title'];
            $headline = $data['article_headline'];
            $body = $data['article_body'];
        }
        $array = [
            'article_id' => $data['article_id'],
            'article_title' => $title,
            'article_headline' => $headline,
            'article_body' => $body,
            'active' => $data['active'],
            'slug' => $data['slug'],
            'views' => $data['views'],
            'user_id' => $data['user_id'],
            'created_at' => $data['created_at'],
            'updated_at' => $data['updated_at'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'role' => $data['role'],
            'media_path' => $data['media_path'],
            'category_name' => $data['category_name'],
            'category_slug' => $data['category_slug'],
            'biography' => $data['biography']
        ];

        return $this->respond(['status' => true, 'message' => lang('App.getSuccess'), 'data' => $array], 200);
    }

    public function create()
    {
        $rules = [
            'category_id' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'article_title' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'article_title_en' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'article_headline' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'article_headline_en' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'article_body' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'article_body_en' => [
                'rules'  => 'required',
                'errors' => []
            ],
        ];

        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $data = [
                'category_id' => $json->category_id,
                'article_image' => $json->article_image ?? null,
                'article_title' => $json->article_title,
                'article_title_en' => $json->article_title_en,
                'article_headline' => $json->article_headline,
                'article_headline_en' => $json->article_headline_en,
                'article_body' => $json->article_body,
                'article_body_en' => $json->article_body_en,
                'active' => 1,
                'user_id' => session()->get('id'),
                'slug' => $this->slugify($json->article_title),
                'views' => 0,
                'year' => date('Y'),
                'month' => date('m')
            ];
        } else {
            $data = [
                'category_id' => $this->request->getPost('category_id'),
                'article_image' => $this->request->getPost('article_image') ?? null,
                'article_title' => $this->request->getPost('article_title'),
                'article_title_en' => $this->request->getPost('article_title_en'),
                'article_headline' => $this->request->getPost('article_headline'),
                'article_headline_en' => $this->request->getPost('article_headline_en'),
                'article_body' => $this->request->getPost('article_body'),
                'article_body_en' => $this->request->getPost('article_body_en'),
                'active' => 1,
                'user_id' => session()->get('id'),
                'slug' => $this->slugify($this->request->getPost('article_title')),
                'views' => 0,
                'year' => date('Y'),
                'month' => date('m')
            ];
        }

        if (!$this->validate($rules)) {
            $response = [
                'status' => false,
                'message' => lang('App.isRequired'),
                'data' => $this->validator->getErrors(),
            ];
            return $this->respond($response, 200);
        } else {
            $this->model->save($data);

            $response = [
                'status' => true,
                'message' => lang('App.saveSuccess'),
                'data' => [],
            ];
            return $this->respond($response, 200);
        }
    }

    public function update($id = NULL)
    {
        $rules = [
            'category_id' => [
                'rules'  => 'required',
                'errors' => []
            ],
            'article_title' => [
                'rules'  => 'required',
                'errors' => []
            ],
        ];

        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $data = [
                'category_id' => $json->category_id,
                'article_image' => $json->article_image,
                'article_title' => $json->article_title,
                'article_title_en' => $json->article_title_en,
                'article_headline' => $json->article_headline,
                'article_headline_en' => $json->article_headline_en,
                'article_body' => $json->article_body,
                'article_body_en' => $json->article_body_en,
                'slug' => $this->slugify($json->article_title),
            ];
        } else {
            $input = $this->request->getRawInput();
            $data = [
                'category_id' => $input['category_id'],
                'article_image' => $input['article_image'],
                'article_title' => $input['article_title'],
                'article_title_en' => $input['article_title_en'],
                'article_headline' => $input['article_headline'],
                'article_headline_en' => $input['article_headline_en'],
                'article_body' =>  $input['article_body'],
                'article_body_en' =>  $input['article_body_en'],
                'slug' => $this->slugify($input['article_title']),
            ];
        }

        if (!$this->validate($rules)) {
            $response = [
                'status' => false,
                'message' => lang('App.updFailed'),
                'data' => $this->validator->getErrors(),
            ];
            return $this->respond($response, 200);
        } else {
            $this->model->update($id, $data);
            $response = [
                'status' => true,
                'message' => lang('App.updSuccess'),
                'data' => [],
            ];
            return $this->respond($response, 200);
        }
    }

    public function setActive($id = NULL)
    {
        if ($this->request->getJSON()) {
            $json = $this->request->getJSON();
            $data = [
                'active' => $json->active,
            ];
        } else {
            $input = $this->request->getRawInput();
            $data = [
                'active' => $input['active'],
            ];
        }

        if ($data > 0) {
            $this->model->update($id, $data);

            $response = [
                'status' => true,
                'message' => lang('App.updSuccess'),
                'data' => []
            ];
            return $this->respond($response, 200);
        } else {
            $response = [
                'status' => false,
                'message' => lang('App.updFailed'),
                'data' => []
            ];
            return $this->respond($response, 200);
        }
    }

    public function delete($id = null)
    {
        $delete = $this->model->find($id);

        if ($delete) {
            $this->model->delete($id);
            $response = [
                'status' => true,
                'message' => lang('App.delSuccess'),
                'data' => [],
            ];
            return $this->respond($response, 200);
        } else {
            $response = [
                'status' => false,
                'message' => lang('App.delFailed'),
                'data' => [],
            ];
            return $this->respond($response, 200);
        }
    }

    /**
     * Generate AI Article using Google Gemini API
     * Menghasilkan artikel IT yang terhumanisasi menggunakan Google Gemini
     */
    public function generateAI()
    {
        $json = $this->request->getJSON();
        $topic    = $json->topic    ?? '';
        $type     = $json->type     ?? 'tutorial';
        $tone     = $json->tone     ?? 'friendly';

        if (empty($topic)) {
            return $this->respond([
                'status'  => false,
                'message' => 'Topik artikel tidak boleh kosong.',
                'data'    => [],
            ], 200);
        }

        try {
            $apiKey = env('GEMINI_API_KEY');
            $client = \Gemini::client($apiKey);

            // Tentukan gaya bahasa
            $toneDesc = match ($tone) {
                'professional' => 'profesional dan formal',
                'casual'       => 'santai dan conversational',
                'educational'  => 'edukatif dan informatif',
                default        => 'ramah, hangat, dan mudah dipahami seperti ditulis oleh manusia berpengalaman',
            };

            $typeDesc = match ($type) {
                'tutorial'    => 'tutorial langkah demi langkah',
                'tips'        => 'tips dan trik praktis',
                'review'      => 'ulasan mendalam',
                'news'        => 'berita dan informasi terkini',
                'explanation' => 'penjelasan konsep',
                default       => 'artikel informatif',
            };

            $prompt = <<<PROMPT
You are a professional IT content writer and translator with over 10 years of experience.
Your task is to write a {$typeDesc} about: "{$topic}".

Writing style: {$toneDesc}.

You MUST write the article in BOTH Indonesian and English.
For the Indonesian version, write naturally like a native Indonesian IT expert (humanized, not robot-like).
For the English version, write naturally like a native English IT expert.

Important guidelines for both versions:
- Write like a real human expert in IT, not a robot.
- Use natural, flowing sentences with varied lengths. Use everyday analogies where appropriate.
- Add personal perspectives like "Menurut pengalaman saya..." / "In my experience...", etc.
- Avoid overly rigid bullet points. Create engaging narratives.
- Include real, practical examples.
- Article length for each version: minimum 600 words, maximum 1200 words.

Output format that MUST be followed (in JSON):
{
  "title_id": "Judul artikel Bahasa Indonesia yang menarik dan SEO-friendly",
  "headline_id": "Ringkasan singkat artikel Bahasa Indonesia (maks 200 karakter)",
  "body_id": "Isi artikel lengkap Bahasa Indonesia dalam format HTML (gunakan tag h2, h3, p, strong, em, ul, ol, li, blockquote). JANGAN sertakan tag html, head, body.",
  "title_en": "An attractive, SEO-friendly article title in English",
  "headline_en": "A brief summary of the article in English (max 200 characters)",
  "body_en": "Full article content in English in HTML format (use h2, h3, p, strong, em, ul, ol, li, blockquote). Do NOT add html, head, body tags."
}

Ensure output is valid JSON without markdown code blocks.
PROMPT;

            $result = $client
                ->generativeModel(model: 'gemini-2.5-flash')
                ->generateContent($prompt);

            $rawText = $result->text();

            // Bersihkan jika ada markdown code block
            $rawText = preg_replace('/^```json\s*/i', '', trim($rawText));
            $rawText = preg_replace('/```\s*$/', '', $rawText);
            $rawText = trim($rawText);

            $decoded = json_decode($rawText, true);

            if (json_last_error() !== JSON_ERROR_NONE || empty($decoded)) {
                return $this->respond([
                    'status'  => false,
                    'message' => 'Gagal memproses respons AI. Coba lagi.',
                    'data'    => ['raw' => $rawText],
                ], 200);
            }

            return $this->respond([
                'status'  => true,
                'message' => 'Artikel AI berhasil digenerate!',
                'data'    => [
                    'title_id'    => $decoded['title_id']    ?? '',
                    'headline_id' => $decoded['headline_id'] ?? '',
                    'body_id'     => $decoded['body_id']     ?? '',
                    'title_en'    => $decoded['title_en']    ?? '',
                    'headline_en' => $decoded['headline_en'] ?? '',
                    'body_en'     => $decoded['body_en']     ?? '',
                ],
            ], 200);
        } catch (\Throwable $e) {
            return $this->respond([
                'status'  => false,
                'message' => 'Error AI: ' . $e->getMessage(),
                'data'    => [],
            ], 200);
        }
    }
}
