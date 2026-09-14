<?php
/**
 * BB Docs — front matter check.
 *
 *   php tools/check_frontmatter.php [repo-root]
 *
 * Validates every page against schema/front-matter.schema.json (the schema file IS the
 * rule: this script reads it, it does not restate it), plus the cross-page invariants a
 * per-file schema cannot express — unique slugs, known sections, no collision between a
 * slug and a legacy anchor, declared placeholders.
 *
 * Exit code 0 = clean, 1 = at least one error. Warnings never fail the build.
 */

require __DIR__ . '/lib_frontmatter.php';

$root = rtrim($argv[1] ?? dirname(__DIR__), '/');
$errors = [];
$warns  = [];

/* ---------------------------------------------------------------- schema -- */
$schemaPath = $root . '/schema/front-matter.schema.json';
$schema = json_decode((string) @file_get_contents($schemaPath), true);
if (!is_array($schema) || empty($schema['properties'])) {
    fwrite(STDERR, "cannot read $schemaPath\n");
    exit(1);
}
$props    = $schema['properties'];
$required = $schema['required'] ?? [];
$extra    = ($schema['additionalProperties'] ?? true) === false;

/* -------------------------------------------------------------- sections -- */
$secRaw = @file_get_contents($root . '/docs.yml');
if ($secRaw === false) {
    fwrite(STDERR, "cannot read $root/docs.yml\n");
    exit(1);
}
$sec = bbdocs_parse_sections($secRaw);
foreach ($sec['errors'] as $e) { $errors[] = $e; }
$sectionIds = [];
foreach ($sec['sections'] as $i => $s) {
    foreach (['id', 'title', 'order', 'summary'] as $k) {
        if (!isset($s[$k])) { $errors[] = "docs.yml: section #" . ($i + 1) . " has no `$k`"; }
    }
    if (isset($s['id'])) {
        if (in_array($s['id'], $sectionIds, true)) { $errors[] = "docs.yml: duplicate section id `{$s['id']}`"; }
        $sectionIds[] = $s['id'];
    }
}
if ($sec['version'] !== 1) { $errors[] = 'docs.yml: `version: 1` is missing or not 1'; }

/* ----------------------------------------------------------------- pages -- */
$col = bbdocs_collect_pages($root);
foreach ($col['errors'] as $e) { $errors[] = $e; }

$slugs = [];
$anchors = [];
$orderBySection = [];

foreach ($col['pages'] as $p) {
    $f  = $p['file'];
    $fm = $p['fm'];

    // -- schema: required, unknown, then per-property rules ------------------
    foreach ($required as $k) {
        if (!array_key_exists($k, $fm)) { $errors[] = "$f: front matter is missing `$k`"; }
    }
    foreach ($fm as $k => $v) {
        if (!isset($props[$k])) {
            if ($extra) { $errors[] = "$f: `$k` is not a front matter key this repository knows (see schema/front-matter.schema.json)"; }
            continue;
        }
        $spec = $props[$k];
        $type = $spec['type'] ?? 'string';

        if ($type === 'integer') {
            if (!is_int($v)) { $errors[] = "$f: `$k` must be a whole number"; continue; }
            if (isset($spec['minimum']) && $v < $spec['minimum']) { $errors[] = "$f: `$k` must be >= {$spec['minimum']}"; }
            if (isset($spec['maximum']) && $v > $spec['maximum']) { $errors[] = "$f: `$k` must be <= {$spec['maximum']}"; }
            continue;
        }
        if ($type === 'array') {
            if (!is_array($v)) { $errors[] = "$f: `$k` must be a list, e.g. `$k: [one, two]`"; continue; }
            if (($spec['uniqueItems'] ?? false) && count($v) !== count(array_unique($v))) {
                $errors[] = "$f: `$k` has a duplicate entry";
            }
            $ipat = $spec['items']['pattern'] ?? null;
            foreach ($v as $item) {
                if (!is_string($item)) { $errors[] = "$f: `$k` must only contain text items"; continue; }
                if ($ipat !== null && !preg_match('/' . str_replace('/', '\/', $ipat) . '/', $item)) {
                    $errors[] = "$f: `$k` item `$item` does not match " . $ipat;
                }
            }
            continue;
        }
        // string
        if (!is_string($v)) { $errors[] = "$f: `$k` must be text (quote it if it looks like a number)"; continue; }
        $len = mb_strlen($v);
        if (isset($spec['minLength']) && $len < $spec['minLength']) { $errors[] = "$f: `$k` is too short (min {$spec['minLength']} chars)"; }
        if (isset($spec['maxLength']) && $len > $spec['maxLength']) { $errors[] = "$f: `$k` is too long ($len chars, max {$spec['maxLength']})"; }
        if (isset($spec['pattern']) && !preg_match('/' . str_replace('/', '\/', $spec['pattern']) . '/u', $v)) {
            $errors[] = "$f: `$k` = `$v` does not match " . $spec['pattern'];
        }
        if (isset($spec['enum']) && !in_array($v, $spec['enum'], true)) {
            $errors[] = "$f: `$k` = `$v` is not one of " . implode(', ', $spec['enum']);
        }
    }

    // -- cross-page invariants ----------------------------------------------
    $slug = is_string($fm['slug'] ?? null) ? $fm['slug'] : null;
    if ($slug !== null) {
        if (isset($slugs[$slug])) { $errors[] = "$f: slug `$slug` is already used by {$slugs[$slug]}"; }
        $slugs[$slug] = $f;
        if (in_array($slug, ['index', 'asset', 'hook', 'search'], true)) {
            $errors[] = "$f: slug `$slug` is reserved by the website's router";
        }
    }
    foreach ((array) ($fm['legacy_anchors'] ?? []) as $a) {
        if (isset($anchors[$a])) { $errors[] = "$f: legacy anchor `$a` is already claimed by {$anchors[$a]}"; }
        $anchors[$a] = $f;
    }
    foreach ((array) ($fm['redirect_from'] ?? []) as $a) {
        if (isset($slugs[$a]) && $slugs[$a] !== $f) { $errors[] = "$f: redirect_from `$a` is a live slug of {$slugs[$a]}"; }
    }

    $section = $fm['section'] ?? null;
    if (is_string($section) && $sectionIds && !in_array($section, $sectionIds, true)) {
        $errors[] = "$f: section `$section` is not declared in docs.yml (" . implode(', ', $sectionIds) . ')';
    }
    $dirSection = basename($p['dir']);
    if (is_string($section) && $dirSection !== 'content' && $section !== $dirSection) {
        $errors[] = "$f: lives in content/$dirSection/ but declares `section: $section` — keep the two the same";
    }

    if ($p['is_index'] && ($fm['order'] ?? null) !== 0) {
        $errors[] = "$f: a section index.md must have `order: 0`";
    }
    if (!$p['is_index'] && ($fm['order'] ?? null) === 0) {
        $errors[] = "$f: `order: 0` is reserved for the section index.md";
    }
    if (is_string($section) && isset($fm['order']) && is_int($fm['order'])) {
        $key = $section . '/' . $fm['order'];
        if (isset($orderBySection[$key])) { $warns[] = "$f: same `order` as {$orderBySection[$key]} inside section `$section` — the tie is broken by filename"; }
        $orderBySection[$key] = $f;
    }
    if (isset($fm['updated']) && is_string($fm['updated'])) {
        $ts = strtotime($fm['updated'] . ' 00:00:00 UTC');
        if ($ts === false) { $errors[] = "$f: `updated` is not a real date"; }
        elseif ($ts > time() + 86400) { $errors[] = "$f: `updated` is in the future"; }
    }
    if (isset($fm['title']) && is_string($fm['title']) && str_ends_with(rtrim($fm['title']), '.')) {
        $warns[] = "$f: `title` ends with a period";
    }

    // -- body ----------------------------------------------------------------
    $body = $p['body'];
    if (trim($body) === '') {
        $errors[] = "$f: the page has no body";
    }
    foreach (bbdocs_headings($body) as $anchor => $h) {
        if ($h['level'] === 1) {
            $errors[] = "$f: `# {$h['text']}` — the page title comes from the front matter, start at `## `";
        }
    }
    if (preg_match('~<\s*(script|iframe|style|object|embed|form|a|div|span|img|br|table)\b~i', $body, $m)) {
        $errors[] = "$f: raw HTML (`<{$m[1]}`) — the website strips it, so it would silently disappear";
    }
    if (str_contains($body, "\t")) {
        $warns[] = "$f: contains a tab character";
    }
    if (preg_match('/[ \t]+$/m', $body)) {
        $warns[] = "$f: has trailing whitespace on at least one line";
    }
    if (!str_ends_with($body, "\n") || str_ends_with($body, "\n\n")) {
        $warns[] = "$f: should end with exactly one newline";
    }

    // -- placeholders --------------------------------------------------------
    $declared = array_map('strval', (array) ($fm['vars'] ?? []));
    preg_match_all('/\{\{\s*([a-z][a-z0-9_]*)\s*\}\}/', $body, $mm);
    $used = array_values(array_unique($mm[1] ?? []));
    foreach ($used as $u) {
        if (!in_array($u, $declared, true)) {
            $errors[] = "$f: `{{{$u}}}` is used but not declared in `vars:` — and the website can only fill placeholders it knows about (open an issue to add one)";
        }
    }
    foreach ($declared as $d) {
        if (!in_array($d, $used, true)) { $warns[] = "$f: `vars:` declares `$d`, which the page never uses"; }
    }
    if (preg_match('/\{\{\s*[^}]*[^a-z0-9_ }][^}]*\}\}/', $body, $bad)) {
        $errors[] = "$f: malformed placeholder " . trim($bad[0]);
    }
}

/* ---------------------------------------------------------------- report -- */
foreach ($sectionIds as $id) {
    $has = false;
    foreach ($col['pages'] as $p) { if (($p['fm']['section'] ?? null) === $id) { $has = true; break; } }
    if (!$has) { $warns[] = "docs.yml: section `$id` has no page"; }
    if (!is_file("$root/content/$id/index.md")) { $errors[] = "content/$id/index.md is missing (every section needs its index page)"; }
}

$n = count($col['pages']);
foreach ($warns as $w)  { echo "warn  $w\n"; }
foreach ($errors as $e) { echo "ERROR $e\n"; }
printf("front matter: %d pages, %d sections, %d error(s), %d warning(s)\n", $n, count($sectionIds), count($errors), count($warns));
exit($errors ? 1 : 0);
