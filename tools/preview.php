<?php
/**
 * BB Docs — local preview.
 *
 *   make preview          →  http://127.0.0.1:8088
 *
 * Renders the pages with league/commonmark configured exactly like the website:
 * HTML input stripped, unsafe links disabled, GFM tables / task lists / footnotes,
 * heading permalinks. It is a preview, not the site — the styling is a deliberately
 * minimal stand-in, and the placeholders ({{like_this}}) are shown as-is.
 *
 * Only binds 127.0.0.1. Never expose it.
 */

require __DIR__ . '/lib_frontmatter.php';

$root = dirname(__DIR__);

/* ------------------------------------------------------------- the files -- */
$autoload = $root . '/vendor/autoload.php';
if (!is_file($autoload)) {
    header('Content-Type: text/plain; charset=utf-8');
    echo "The preview needs league/commonmark, the same Markdown renderer the website uses.\n\n"
       . "    composer install        (or: make deps)\n\n"
       . "You do not need it to contribute: GitHub's own preview of a .md file is faithful\n"
       . "enough for almost every edit, and `make check` runs without any dependency.\n";
    exit;
}
require $autoload;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Footnote\FootnoteExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\MarkdownConverter;

/* ------------------------------------------------------------- the pages -- */
$col = bbdocs_collect_pages($root);
$bySlug = [];
foreach ($col['pages'] as $p) {
    if (isset($p['fm']['slug'])) { $bySlug[(string) $p['fm']['slug']] = $p; }
}
$sections = bbdocs_parse_sections((string) file_get_contents($root . '/docs.yml'))['sections'];

$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
if (str_starts_with($path, '/assets/') && is_file($root . $path)) {
    return false;                                  // let the built-in server serve it
}

$env = new Environment([
    'html_input'         => 'strip',
    'allow_unsafe_links' => false,
    'max_nesting_level'  => 50,
    'heading_permalink'  => ['html_class' => 'anchor', 'symbol' => '#', 'insert' => 'after'],
]);
$env->addExtension(new CommonMarkCoreExtension());
$env->addExtension(new GithubFlavoredMarkdownExtension());
$env->addExtension(new FootnoteExtension());
$env->addExtension(new HeadingPermalinkExtension());
$md = new MarkdownConverter($env);

$slug = trim($path, '/');
header('Content-Type: text/html; charset=utf-8');

$css = <<<CSS
body{max-width:820px;margin:0 auto;padding:24px 16px 80px;
 font:15px/1.6 system-ui,-apple-system,"Segoe UI",sans-serif;color:#15161B;background:#F3F3EC}
a{color:#2F6BFF}code{background:#EDEDE5;border:1px solid #15161B;border-radius:6px;padding:0 4px;font-size:.9em}
pre{background:#15161B;color:#D7D8CE;padding:10px 14px;border-radius:10px;overflow-x:auto}
pre code{background:none;border:0;color:inherit}
table{border-collapse:collapse;width:100%;margin:12px 0}th,td{border-bottom:1px solid #d9d9cf;padding:6px 9px;text-align:left}
th{border-bottom:2px solid #15161B;font-size:.85em;text-transform:uppercase;letter-spacing:.08em}
blockquote{margin:10px 0;padding:6px 12px;border-left:3px solid #15161B;background:#EDEDE5}
.warn{background:#ffe9c7;border:2px solid #C18A1F;padding:8px 12px;border-radius:8px}
.meta{color:#6E727E;font-size:13px}img{max-width:100%}
CSS;

echo "<!doctype html><meta charset=utf-8><meta name=viewport content='width=device-width,initial-scale=1'>";
echo "<style>$css</style>";

if ($col['errors']) {
    echo '<p class="warn"><strong>' . count($col['errors']) . ' problem(s) found by the parser — run <code>make check</code>.</strong></p>';
}

if ($slug === '' || $slug === 'index') {
    echo '<h1>BB Docs — local preview</h1><p class="meta">' . count($bySlug) . ' pages. This is not the site: styling is a stand-in.</p>';
    foreach ($sections as $s) {
        $id = (string) ($s['id'] ?? '');
        echo '<h2>' . htmlspecialchars((string) ($s['title'] ?? $id), ENT_QUOTES) . '</h2><ul>';
        $pages = array_filter($col['pages'], fn($p) => ($p['fm']['section'] ?? null) === $id);
        usort($pages, fn($a, $b) => [$a['fm']['order'] ?? 999, $a['file']] <=> [$b['fm']['order'] ?? 999, $b['file']]);
        foreach ($pages as $p) {
            $sl = htmlspecialchars((string) ($p['fm']['slug'] ?? ''), ENT_QUOTES);
            echo '<li><a href="/' . $sl . '">' . htmlspecialchars((string) ($p['fm']['title'] ?? $p['file']), ENT_QUOTES) . '</a> '
               . '<span class="meta">— ' . htmlspecialchars((string) ($p['fm']['summary'] ?? ''), ENT_QUOTES) . '</span></li>';
        }
        echo '</ul>';
    }
    exit;
}

if (!isset($bySlug[$slug])) {
    http_response_code(404);
    echo '<h1>404</h1><p>No page with slug <code>' . htmlspecialchars($slug, ENT_QUOTES) . '</code>. <a href="/">Index</a></p>';
    exit;
}

$p = $bySlug[$slug];
echo '<p class="meta"><a href="/">← index</a> · ' . htmlspecialchars($p['file'], ENT_QUOTES) . '</p>';
echo '<h1>' . htmlspecialchars((string) ($p['fm']['title'] ?? ''), ENT_QUOTES) . '</h1>';
echo $md->convert($p['body'])->getContent();
echo '<hr><p class="meta">section <code>' . htmlspecialchars((string) ($p['fm']['section'] ?? ''), ENT_QUOTES)
   . '</code> · reviewed ' . htmlspecialchars((string) ($p['fm']['updated'] ?? ''), ENT_QUOTES)
   . ' · CC BY-SA 4.0</p>';
