<?php echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach($paginasEstaticas as $p)
    <url>
        <loc>{{ $p['loc'] }}</loc>
        <changefreq>{{ $p['changefreq'] }}</changefreq>
        <priority>{{ $p['priority'] }}</priority>
    </url>
@endforeach
@foreach($posts as $post)
    <url>
        <loc>{{ route('blog.post', $post->slug) }}</loc>
        <lastmod>{{ $post->updated_at->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
@endforeach
</urlset>
