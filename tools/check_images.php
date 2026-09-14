<?php
/**
 * BB Docs — image budget check.
 *
 *   php tools/check_images.php [repo-root]
 *
 * A documentation repository dies of weight, not of prose. The rules:
 *   - images live under assets/, nowhere else;
 *   - .png .jpg .jpeg .webp .gif only — no SVG (the site refuses to serve it: an SVG is a
 *     script container) and no video;
 *   - 500 KB per file, 8 MB for the whole assets/ tree;
 *   - every image must actually be referenced by a page, and every referenced image must
 *     exist (the existence half is also covered by check_links.php);
 *   - every image needs alt text.
 *
 * Exit code 0 = clean, 1 = at least one error.
 */

require __DIR__ . '/lib_frontmatter.php';

const BBDOCS_MAX_IMAGE_BYTES = 512000;      // 500 KB
const BBDOCS_MAX_ASSETS_BYTES = 8388608;    // 8 MB
const BBDOCS_IMAGE_EXT = ['png', 'jpg', 'jpeg', 'webp', 'gif'];

$root   = rtrim($argv[1] ?? dirname(__DIR__), '/');
$errors = [];
$warns  = [];

/* ------------------------------------------------------------- the files -- */
$assets = [];
$total  = 0;
$dir    = $root . '/assets';
if (is_dir($dir)) {
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS));
    foreach ($it as $f) {
        /** @var SplFileInfo $f */
        if (!$f->isFile()) { continue; }
        $rel = substr($f->getPathname(), strlen($root) + 1);
        if ($f->getFilename() === '.gitkeep') { continue; }
        $ext = strtolower($f->getExtension());
        $size = $f->getSize();
        $total += $size;
        $assets[$rel] = $size;

        if (!in_array($ext, BBDOCS_IMAGE_EXT, true)) {
            $errors[] = sprintf('%s: .%s is not an allowed asset type (%s)', $rel, $ext, implode(', ', BBDOCS_IMAGE_EXT));
        }
        if ($size > BBDOCS_MAX_IMAGE_BYTES) {
            $errors[] = sprintf('%s: %d KB — over the %d KB per-image budget, re-export it smaller', $rel, (int) round($size / 1024), (int) (BBDOCS_MAX_IMAGE_BYTES / 1024));
        }
        if ($f->getFilename() !== strtolower($f->getFilename()) || preg_match('/[^a-z0-9._-]/', strtolower($f->getFilename()))) {
            $errors[] = "$rel: file name must be lowercase letters, digits, dot, dash and underscore only";
        }
    }
}
if ($total > BBDOCS_MAX_ASSETS_BYTES) {
    $errors[] = sprintf('assets/ totals %d KB — over the %d KB budget for the whole tree', (int) round($total / 1024), (int) (BBDOCS_MAX_ASSETS_BYTES / 1024));
}

/* ------------------------------------------------------------ the pages --- */
$col  = bbdocs_collect_pages($root);
$used = [];
foreach ($col['pages'] as $p) {
    $file = $p['file'];
    $base = dirname($root . '/' . $file);
    if (!preg_match_all('/!\[([^\]]*)\]\(\s*([^)\s]+)(?:\s+"[^"]*")?\s*\)/', $p['body'], $m, PREG_SET_ORDER)) {
        continue;
    }
    foreach ($m as $hit) {
        [$all, $alt, $target] = $hit;
        if (trim($alt) === '') {
            $errors[] = "$file: image $target has no alt text — write what it shows, it is read out loud and shown when the image fails";
        }
        if (preg_match('~^(https?:)?//~i', $target)) {
            $errors[] = "$file: image $target is hosted elsewhere — commit it under assets/ instead (external images rot, and they leak every reader's IP)";
            continue;
        }
        $abs = realpath($base . '/' . explode('#', $target)[0]);
        if ($abs === false) { continue; }   // check_links.php reports the broken path
        $rel = substr($abs, strlen((string) realpath($root)) + 1);
        $used[$rel] = true;
    }
}

foreach (array_keys($assets) as $rel) {
    if (!isset($used[$rel])) {
        $warns[] = "$rel: committed but no page uses it";
    }
}

foreach ($warns as $w)  { echo "warn  $w\n"; }
foreach ($errors as $e) { echo "ERROR $e\n"; }
printf("images: %d file(s), %d KB total, %d error(s), %d warning(s)\n",
    count($assets), (int) round($total / 1024), count($errors), count($warns));
exit($errors ? 1 : 0);
