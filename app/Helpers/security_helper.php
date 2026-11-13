<?php

/**
 * Helper keamanan: sanitasi HTML, validasi warna dan URL.
 * Dimuat otomatis lewat BaseController::$helpers.
 */

if (! function_exists('sanitize_html')) {
    /**
     * Menyaring HTML dari editor (CKEditor) dengan daftar tag yang diizinkan.
     * Tag/atribut lain dibuang, script/style dibuang beserta isinya,
     * dan tautan hanya boleh http, https, mailto, atau relatif.
     */
    function sanitize_html(?string $html): string
    {
        $html = (string) $html;

        if (trim($html) === '') {
            return '';
        }

        // tag yang diizinkan => atribut yang diizinkan
        $allowed = [
            'p' => [], 'br' => [], 'h1' => [], 'h2' => [], 'h3' => [], 'h4' => [], 'h5' => [], 'h6' => [],
            'strong' => [], 'b' => [], 'em' => [], 'i' => [], 'u' => [],
            'ul' => [], 'ol' => [], 'li' => [], 'blockquote' => [],
            'a' => ['href', 'title'],
        ];

        // tag berbahaya yang dibuang beserta seluruh isinya
        $drop = [
            'script', 'style', 'iframe', 'frame', 'frameset', 'object', 'embed', 'applet',
            'form', 'input', 'button', 'textarea', 'select', 'option', 'link', 'meta',
            'base', 'svg', 'math', 'template', 'noscript', 'title', 'head',
        ];

        $previous = libxml_use_internal_errors(true);

        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->loadHTML(
            '<?xml encoding="UTF-8"><div id="sanitize-root">' . $html . '</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );

        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $doc->getElementsByTagName('div')->item(0);

        if ($root === null) {
            return esc(strip_tags($html));
        }

        sanitize_html_node($root, $allowed, $drop);

        $output = '';
        foreach ($root->childNodes as $child) {
            $output .= $doc->saveHTML($child);
        }

        return $output;
    }
}

if (! function_exists('sanitize_html_node')) {
    /**
     * Fungsi rekursif pendukung sanitize_html().
     *
     * @param array<string, list<string>> $allowed
     * @param list<string>                $drop
     */
    function sanitize_html_node(DOMNode $node, array $allowed, array $drop): void
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child instanceof DOMText && ! ($child instanceof DOMCdataSection)) {
                continue; // teks biasa aman
            }

            if (! ($child instanceof DOMElement)) {
                $node->removeChild($child); // komentar, CDATA, instruksi proses
                continue;
            }

            $tag = strtolower($child->nodeName);

            if (in_array($tag, $drop, true)) {
                $node->removeChild($child);
                continue;
            }

            sanitize_html_node($child, $allowed, $drop);

            if (! isset($allowed[$tag])) {
                // tag tidak diizinkan: buang tag-nya, pertahankan isinya
                while ($child->firstChild !== null) {
                    $node->insertBefore($child->firstChild, $child);
                }
                $node->removeChild($child);
                continue;
            }

            foreach (iterator_to_array($child->attributes) as $attr) {
                if (! in_array(strtolower($attr->name), $allowed[$tag], true)) {
                    $child->removeAttributeNode($attr);
                }
            }

            if ($tag === 'a') {
                $href = $child->getAttribute('href');
                $test = preg_replace('/[\x00-\x20]+/', '', $href) ?? '';

                if ($test === '' || ! preg_match('#^(https?://|mailto:|/|\#)#i', $test)) {
                    $child->removeAttribute('href');
                } else {
                    $child->setAttribute('rel', 'noopener noreferrer nofollow ugc');
                }
            }
        }
    }
}

if (! function_exists('safe_color')) {
    /**
     * Mengembalikan warna hex (#RGB / #RRGGBB) yang valid, atau nilai default.
     * Dipakai untuk nilai yang dicetak di dalam atribut style.
     */
    function safe_color(?string $color, string $default = '#cbd5e1'): string
    {
        $color = trim((string) $color);

        return preg_match('/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $color) ? $color : $default;
    }
}

if (! function_exists('safe_url')) {
    /**
     * Hanya mengizinkan URL http/https. Selain itu dikembalikan string kosong.
     */
    function safe_url(?string $url): string
    {
        $url = trim((string) $url);

        return preg_match('#^https?://#i', $url) ? $url : '';
    }
}
