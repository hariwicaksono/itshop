<?php
$datetime1 = new DateTime(date('Y-m-d H:i:s'));
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
    <url>
        <loc><?= base_url() ?></loc>
        <lastmod><?= $datetime1->format(DATE_ATOM); ?></lastmod>
        <changefreq>daily</changefreq>
        <priority>0.1</priority>
    </url>
    <url>
        <loc><?= base_url('login'); ?></loc>
        <lastmod><?= $datetime1->format(DATE_ATOM); ?></lastmod>
        <changefreq>daily</changefreq>
        <priority>0.5</priority>
    </url>
    <url>
        <loc><?= base_url('register'); ?></loc>
        <lastmod><?= $datetime1->format(DATE_ATOM); ?></lastmod>
        <changefreq>daily</changefreq>
        <priority>0.5</priority>
    </url>
    <url>
        <loc><?= base_url('password/reset'); ?></loc>
        <lastmod><?= $datetime1->format(DATE_ATOM); ?></lastmod>
        <changefreq>daily</changefreq>
        <priority>0.5</priority>
    </url>
    <?php foreach ($pages as $item) {
        $datetime = new DateTime($item['created_at']); ?>
        <url>
            <loc><?= base_url() . $item['slug']; ?></loc>
            <lastmod><?= $datetime->format(DATE_ATOM); ?></lastmod>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
        </url>
    <?php } ?>
    <?php foreach ($products as $item) {
        $datetime = new DateTime($item['created_at']); ?>
        <url>
            <loc><?= base_url() . $item['category_slug'] . '/' . $item['slug']; ?></loc>
            <lastmod><?= $datetime->format(DATE_ATOM); ?></lastmod>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
        </url>
    <?php } ?>
    <?php foreach ($articles as $item) {
        $datetime = new DateTime($item['created_at']); ?>
        <url>
            <loc><?= base_url() . $item['category_slug'] . '/' . $item['year'] . '/' . $item['month'] . '/' . $item['slug']; ?></loc>
            <lastmod><?= $datetime->format(DATE_ATOM); ?></lastmod>
            <changefreq>daily</changefreq>
            <priority>0.5</priority>
        </url>
    <?php } ?>
</urlset>