<?php
/**
 * BB Docs — internal link check.
 *
 *   php tools/check_links.php [repo-root]
 *
 * Every relative link between pages must resolve to a file that exists, and — when it
 * carries a `#fragment` — to a heading that exists in that file. Nothing here talks to the
 * network: external links are listed, never fetched (a link checker that calls out to
 * 200 hosts on every pull request is a link checker that gets rate-limited and ignored).
 *
 * Exit code 0 = clean, 1 = at least one broken internal link.
 */

require __DIR__ . '/lib_frontmatter.php';

$root = rtrim($argv[1] ?? dirname(__DIR__), '/');
$col  = bbdocs_collect_pages($root);

$errors = [];
$warns  = [];
$ext    = [];

/* index every page by its path, its slug and its headings */
$byFile = [];
foreach ($col['pages'] as $p) {
    $byFile[$p['file']] = [
        'slug'     => $p['fm']['slug'] ?? null,
        'headings' => bbdocs_headings($p['body']),
    ];
}
$slugs = [];
foreach ($col['pages'] as $p) {
    if (isset($p['fm']['slug'])) { $slugs[(string) $p['fm']['slug']] = $p['file']; }
}

/** Strip fenced code blocks so an example link inside ``` is not treated as a link. */
function bbdocs_strip_fences(string $body): string
{
    $out = [];
    $fence = null;
    foreach (explode("\n", $body) as $line) {
        if (preg_match('/^(```|~~~)/', $line, $m)) {
            if ($fence === null)                  { $fence = $m[1]; }
            elseif (str_starts_with($line, $fence)) { $fence = null; }
            $out[] = '';
            continue;
        }
        $out[] = $fence === null ? $line : '';
    }
    return implode("\n", $out);
}

foreach ($col['pages'] as $p) {
    $file = $p['file'];
    $dir  = dirname($root . '/' . $file);
    $body = bbdocs_strip_fences($p['body']);
    $self = $byFile[$file];

    // Markdown inline links and images: [label](target) / ![alt](target)
    if (!preg_match_all('/(!?)\[(?:[^\]\\\\]|\\\\.)*\]\(\s*([^)\s]+)(?:\s+"[^"]*")?\s*\)/', $body, $m, PREG_SET_ORDER)) {
        $m = [];
    }
    foreach ($m as $hit) {
        $isImage = $hit[1] === '!';
        $target  = $hit[2];

        if (preg_match('~^(https?:)?//~i', $target)) {
            // Not fetched, not judged: whether a third-party site speaks https is not
            // something this repository can test offline, and a warning nobody can act on
            // is a warning everybody learns to skip.
            $ext[] = $target;
            continue;
        }
        if (preg_match('~^(mailto|tel):~i', $target)) { continue; }
        if (preg_match('~^[a-z][a-z0-9+.-]*:~i', $target)) {
            $errors[] = "$file: link scheme not allowed: $target";
            continue;
        }

        // same-page anchor
        if (str_starts_with($target, '#')) {
            $frag = substr($target, 1);
            if (!isset($self['headings'][$frag])) {
                $errors[] = "$file: #$frag does not match any heading in this page";
            }
            continue;
        }

        [$path, $frag] = array_pad(explode('#', $target, 2), 2, null);
        $path = rawurldecode($path);
        if ($path === '') { continue; }

        $abs = realpath($dir . '/' . $path);
        if ($abs === false || !str_starts_with($abs, realpath($root) . DIRECTORY_SEPARATOR)) {
            $errors[] = "$file: $target does not resolve to a file in this repository";
            continue;
        }
        $rel = substr($abs, strlen((string) realpath($root)) + 1);

        if ($isImage) {
            if (!str_starts_with($rel, 'assets/')) {
                $errors[] = "$file: image $target must live under assets/";
            }
            continue;
        }
        if (!str_ends_with(strtolower($rel), '.md')) {
            $errors[] = "$file: $target is not a Markdown page (link pages by their .md path)";
            continue;
        }
        if (!isset($byFile[$rel])) {
            $errors[] = "$file: $target resolves outside content/ ($rel)";
            continue;
        }
        if ($frag !== null && $frag !== '' && !isset($byFile[$rel]['headings'][$frag])) {
            $errors[] = "$file: $rel has no heading anchored `#$frag`";
        }
    }

    // A page must not hard-code the site's own docs URL: use the .md path instead.
    if (preg_match('~biterbattles\.org[^\s)]*r=docs~i', $body, $bad)) {
        $errors[] = "$file: hard-coded docs URL (" . $bad[0] . ") — link the .md file instead, so the link works on GitHub too";
    }
    // Reference-style links are not resolved by this checker; ban them rather than lie.
    if (preg_match('/^\[[^\]]+\]:\s+\S+/m', $body)) {
        $warns[] = "$file: reference-style link definition — inline links are checked, these are not";
    }
}

$extU = array_values(array_unique($ext));
sort($extU);
foreach ($warns as $w)  { echo "warn  $w\n"; }
foreach ($errors as $e) { echo "ERROR $e\n"; }
printf("links: %d pages, %d external link(s) (not fetched), %d error(s), %d warning(s)\n",
    count($col['pages']), count($extU), count($errors), count($warns));
if (in_array('-v', $argv, true)) {
    foreach ($extU as $u) { echo "  ext  $u\n"; }
}
exit($errors ? 1 : 0);
