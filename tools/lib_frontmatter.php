<?php
/**
 * BB Docs — front matter and section-manifest reader.
 *
 * A deliberately small YAML subset, not a YAML library. It accepts exactly what
 * `schema/front-matter.schema.json` describes and refuses everything else, which is why it
 * fits in one file with no dependency: a documentation repository does not need anchors,
 * multi-line scalars or nested maps, and every one of those is a way to smuggle something
 * past a reviewer.
 *
 * Accepted in a front matter block:
 *   key: bare scalar          (int if it looks like one, true/false, otherwise a string)
 *   key: "quoted string"      (double or single quotes, no escape sequences)
 *   key: [a, b, "c d"]        (flow list of scalars, may be empty: [])
 *   # a comment line
 *
 * Refused, loudly: tabs, duplicate keys, nested indentation, block lists, multi-line
 * scalars, anything after the closing `---`.
 *
 * The website has its own independent copy of this parser: it never loads PHP from this
 * repository. This copy is deliberately the stricter of the two, so a page CI accepts is a
 * page the site can read.
 *
 * Licence: CC BY-SA 4.0, like the rest of the repository.
 */

/**
 * Split a page into front matter and body.
 *
 * @return array{fm: array<string,mixed>, body: string, line0: int, errors: string[]}
 */
function bbdocs_parse_page(string $raw, string $label = 'page'): array
{
    $out = ['fm' => [], 'body' => '', 'line0' => 0, 'errors' => []];

    // A UTF-8 BOM is invisible in an editor and breaks the `---` test.
    if (str_starts_with($raw, "\xEF\xBB\xBF")) {
        $out['errors'][] = "$label: starts with a UTF-8 BOM — save the file without one";
        $raw = substr($raw, 3);
    }
    $raw = str_replace("\r\n", "\n", $raw);

    if (!str_starts_with($raw, "---\n")) {
        $out['errors'][] = "$label: must start with a front matter block (a line containing exactly ---)";
        $out['body'] = $raw;
        return $out;
    }

    $lines = explode("\n", $raw);
    $end   = null;
    for ($i = 1, $n = count($lines); $i < $n; $i++) {
        if ($lines[$i] === '---') { $end = $i; break; }
    }
    if ($end === null) {
        $out['errors'][] = "$label: the front matter block is never closed (expected a line containing exactly ---)";
        $out['body'] = $raw;
        return $out;
    }

    for ($i = 1; $i < $end; $i++) {
        $line = $lines[$i];
        $no   = $i + 1;
        if (trim($line) === '' || str_starts_with(ltrim($line), '#')) {
            continue;
        }
        if (str_contains($line, "\t")) {
            $out['errors'][] = "$label:$no: tab character in the front matter — use spaces";
            continue;
        }
        if ($line !== ltrim($line)) {
            $out['errors'][] = "$label:$no: indented line — this front matter has no nested values";
            continue;
        }
        $pos = strpos($line, ':');
        if ($pos === false) {
            $out['errors'][] = "$label:$no: not a `key: value` line";
            continue;
        }
        $key = substr($line, 0, $pos);
        $val = trim(substr($line, $pos + 1));
        if (!preg_match('/^[a-z][a-z0-9_]*$/', $key)) {
            $out['errors'][] = "$label:$no: `$key` is not a valid key (lowercase letters, digits and _ only)";
            continue;
        }
        if (array_key_exists($key, $out['fm'])) {
            $out['errors'][] = "$label:$no: `$key` is defined twice";
            continue;
        }
        if ($val === '') {
            $out['errors'][] = "$label:$no: `$key` has no value";
            continue;
        }
        $parsed = bbdocs_scalar_or_list($val, $label, $no, $out['errors']);
        $out['fm'][$key] = $parsed;
    }

    $out['body']  = implode("\n", array_slice($lines, $end + 1));
    $out['line0'] = $end + 1;      // body line N of the file is line N + line0
    return $out;
}

/** Parse one front matter value: a flow list, a quoted string, or a bare scalar. */
function bbdocs_scalar_or_list(string $val, string $label, int $no, array &$errors): mixed
{
    if (str_starts_with($val, '[')) {
        if (!str_ends_with($val, ']')) {
            $errors[] = "$label:$no: list is not closed with ]";
            return [];
        }
        $inner = trim(substr($val, 1, -1));
        if ($inner === '') {
            return [];
        }
        $items = [];
        foreach (explode(',', $inner) as $piece) {
            $piece = trim($piece);
            if ($piece === '') {
                $errors[] = "$label:$no: empty item in the list";
                continue;
            }
            $items[] = bbdocs_scalar($piece);
        }
        return $items;
    }
    if (str_starts_with($val, '{')) {
        $errors[] = "$label:$no: nested maps are not allowed in front matter";
        return '';
    }
    return bbdocs_scalar($val);
}

/** Parse one scalar. Quotes are literal: there are no escape sequences. */
function bbdocs_scalar(string $v): mixed
{
    $len = strlen($v);
    if ($len >= 2 && (($v[0] === '"' && $v[$len - 1] === '"') || ($v[0] === "'" && $v[$len - 1] === "'"))) {
        return substr($v, 1, -1);
    }
    if ($v === 'true')  { return true; }
    if ($v === 'false') { return false; }
    if (preg_match('/^-?[0-9]+$/', $v)) { return (int) $v; }
    return $v;
}

/**
 * Read docs.yml: `version: 1` and a list of section maps.
 *
 * @return array{version:int, sections: array<int,array<string,mixed>>, errors: string[]}
 */
function bbdocs_parse_sections(string $raw, string $label = 'docs.yml'): array
{
    $out = ['version' => 0, 'sections' => [], 'errors' => []];
    $raw = str_replace("\r\n", "\n", $raw);
    $cur = null;
    $in  = false;

    foreach (explode("\n", $raw) as $i => $line) {
        $no = $i + 1;
        if (trim($line) === '' || str_starts_with(ltrim($line), '#')) {
            continue;
        }
        if (str_contains($line, "\t")) {
            $out['errors'][] = "$label:$no: tab character — use spaces";
            continue;
        }
        if (preg_match('/^version:\s*(\d+)\s*$/', $line, $m)) {
            $out['version'] = (int) $m[1];
            continue;
        }
        if (preg_match('/^sections:\s*$/', $line)) {
            $in = true;
            continue;
        }
        if (!$in) {
            $out['errors'][] = "$label:$no: unexpected line before `sections:`";
            continue;
        }
        if (preg_match('/^\s{2}-\s+([a-z][a-z0-9_]*):\s*(.*)$/', $line, $m)) {
            if ($cur !== null) { $out['sections'][] = $cur; }
            $cur = [$m[1] => bbdocs_scalar_or_list(trim($m[2]), $label, $no, $out['errors'])];
            continue;
        }
        if (preg_match('/^\s{4}([a-z][a-z0-9_]*):\s*(.*)$/', $line, $m)) {
            if ($cur === null) {
                $out['errors'][] = "$label:$no: value outside of a section entry";
                continue;
            }
            if (array_key_exists($m[1], $cur)) {
                $out['errors'][] = "$label:$no: `{$m[1]}` is defined twice in the same section";
                continue;
            }
            $cur[$m[1]] = bbdocs_scalar_or_list(trim($m[2]), $label, $no, $out['errors']);
            continue;
        }
        $out['errors'][] = "$label:$no: cannot read this line (expected `  - id: x` or `    key: value`)";
    }
    if ($cur !== null) { $out['sections'][] = $cur; }
    return $out;
}

/**
 * Walk content/ and build the page list.
 *
 * @return array{pages: array<int,array<string,mixed>>, errors: string[]}
 */
function bbdocs_collect_pages(string $root): array
{
    $res = ['pages' => [], 'errors' => []];
    $dir = $root . '/content';
    if (!is_dir($dir)) {
        $res['errors'][] = 'content/ directory not found';
        return $res;
    }
    $files = [];
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS));
    foreach ($it as $f) {
        /** @var SplFileInfo $f */
        if ($f->isFile() && strtolower($f->getExtension()) === 'md') {
            $files[] = $f->getPathname();
        } elseif ($f->isFile()) {
            $res['errors'][] = 'content/ holds a non-Markdown file: ' . substr($f->getPathname(), strlen($root) + 1);
        }
    }
    sort($files);

    foreach ($files as $path) {
        $rel = substr($path, strlen($root) + 1);
        $raw = (string) file_get_contents($path);
        $p   = bbdocs_parse_page($raw, $rel);
        foreach ($p['errors'] as $e) { $res['errors'][] = $e; }
        $res['pages'][] = [
            'file'    => $rel,
            'dir'     => dirname($rel),
            'is_index' => basename($rel) === 'index.md',
            'fm'      => $p['fm'],
            'body'    => $p['body'],
            'line0'   => $p['line0'],
        ];
    }
    return $res;
}

/** GitHub-compatible heading anchor, used by the link checker and the site alike. */
function bbdocs_slugify_heading(string $text): string
{
    $t = strtolower(trim($text));
    $t = preg_replace('/`([^`]*)`/u', '$1', $t) ?? $t;          // inline code keeps its text
    $t = preg_replace('/\[([^\]]*)\]\([^)]*\)/u', '$1', $t) ?? $t; // links keep their label
    $t = str_replace(['*', '_', '~'], '', $t);
    $t = preg_replace('/[^\p{L}\p{N} \-]/u', '', $t) ?? $t;
    $t = str_replace(' ', '-', trim($t));
    $t = preg_replace('/-+/', '-', $t) ?? $t;
    return trim($t, '-');
}

/** Every `## heading` of a body, as anchor => title (fenced code blocks skipped). */
function bbdocs_headings(string $body): array
{
    $out   = [];
    $fence = null;
    foreach (explode("\n", $body) as $line) {
        $t = rtrim($line);
        if (preg_match('/^(```|~~~)/', $t, $m)) {
            if ($fence === null)            { $fence = $m[1]; }
            elseif (str_starts_with($t, $fence)) { $fence = null; }
            continue;
        }
        if ($fence !== null) { continue; }
        if (preg_match('/^(#{1,6})\s+(.*)$/', $t, $m)) {
            $anchor = bbdocs_slugify_heading($m[2]);
            if ($anchor !== '') { $out[$anchor] = ['level' => strlen($m[1]), 'text' => trim($m[2])]; }
        }
    }
    return $out;
}
