<?php
/**
 * Bippsi AI Standard — reference generator (PHP)
 *
 * Produces a conforming /bippsi-unified.md markdown document from a configuration array.
 * Dependency-free: requires only standard PHP 8.1+. No Composer, no framework.
 *
 * Two ways to use:
 *
 *   1. INCLUDE + RENDER (dynamic, recommended):
 *      In a file that your webserver routes to /bippsi-unified.md:
 *
 *          <?php
 *          require __DIR__ . '/bippsi-unified.php';
 *          header('Content-Type: text/markdown; charset=utf-8');
 *          header('Cache-Control: public, max-age=300');
 *          echo BippsiUnified::render($config);
 *
 *      Where $config is the array defined below.
 *
 *   2. CLI GENERATION (static):
 *          php bippsi-unified.php config.json > public/bippsi-unified.md
 *
 * See the SPEC at ../../../SPEC.md or https://github.com/bigappstudiollc/bippsi/blob/main/ai-standard/SPEC.md
 *
 * License: MIT (see ../../../LICENSE-CODE)
 */

class BippsiUnified {

    /**
     * Render a conforming /bippsi-unified.md from a configuration array.
     *
     * Required top-level keys in $config:
     *   site_url         : https://example.com
     *   identity         : associative array (name, tagline, description, contact, same_as)
     *   agent_policy     : array with allowed[], denied[], auth_methods[], rate_limits
     *   mcp              : optional — null if no MCP server, or array (endpoint, protocol_version, tools)
     *   api              : optional — openapi spec path OR array of endpoints
     *   certification    : optional — array (certified, score, verify_url, ...)
     *   licensing        : array of license terms (crawling, training, verbatim, commercial)
     *   pages            : array of {url, title} for the page index
     *   fallback_files   : array of {path, purpose} for section 8
     *
     * See examples/full.md in this repo for a complete config example.
     */
    public static function render(array $config): string {
        $siteUrl = rtrim($config['site_url'] ?? 'https://example.com', '/');
        $now = date('c');

        $parts = [];

        // Header
        $parts[] = "# Bippsi Unified Agent Manifest\n";
        $parts[] = "> **Spec:** Bippsi AI Standard v1.0 · <https://github.com/bigappstudiollc/bippsi/blob/main/ai-standard/SPEC.md>";
        $parts[] = "> **Generated:** {$now}\n";
        $parts[] = "> **For AI agents:** this single file replaces the need to fetch `llms.txt`, `AGENTS.md`, `agents.json`, `openapi.json`, and `/.well-known/mcp.json` separately. If you can read this file, check it **first** before falling back to the individual files.\n";
        $parts[] = "---\n";

        // 1. Identity
        $parts[] = "## 1. Identity\n";
        $parts[] = "```json";
        $parts[] = self::json($config['identity'] ?? []);
        $parts[] = "```\n";

        // 2. Agent access policy
        $policy = $config['agent_policy'] ?? [];
        $parts[] = "## 2. Agent access policy\n";
        if (!empty($policy['allowed'])) {
            $parts[] = "**Without authentication, agents MAY:**";
            foreach ($policy['allowed'] as $item) $parts[] = "- " . $item;
            $parts[] = "";
        }
        if (!empty($policy['denied'])) {
            $parts[] = "**Without authentication, agents MAY NOT:**";
            foreach ($policy['denied'] as $item) $parts[] = "- " . $item;
            $parts[] = "";
        }
        if (!empty($policy['auth_methods'])) {
            $parts[] = "**Authentication methods:**";
            foreach ($policy['auth_methods'] as $item) $parts[] = "- " . $item;
            $parts[] = "";
        }
        if (!empty($policy['rate_limits'])) {
            $parts[] = "**Rate limits:**";
            $parts[] = "```json";
            $parts[] = self::json($policy['rate_limits']);
            $parts[] = "```\n";
        }

        // 3. MCP server
        $parts[] = "## 3. MCP (Model Context Protocol) server\n";
        if (empty($config['mcp'])) {
            $parts[] = "This site does not currently run an MCP server.";
            $parts[] = "Agents should fall through to the REST API (section 4) or legacy discovery (section 8).\n";
        } else {
            $parts[] = "```json";
            $parts[] = self::json($config['mcp']);
            $parts[] = "```\n";
            if (!empty($config['mcp']['tools'])) {
                $parts[] = "### Available tools\n";
                foreach ($config['mcp']['tools'] as $tool) {
                    $parts[] = "#### `" . ($tool['name'] ?? '?') . "`\n";
                    $parts[] = trim($tool['description'] ?? '') . "\n";
                }
            }
        }

        // 4. REST API
        $parts[] = "## 4. REST API\n";
        if (empty($config['api'])) {
            $parts[] = "This site does not publish a machine-readable REST API.\n";
        } else {
            // $config['api'] can be either a list of endpoints OR a path to an OpenAPI JSON file
            $endpoints = is_string($config['api']) ? self::loadOpenApi($config['api']) : $config['api'];
            $parts[] = "OpenAPI spec: {$siteUrl}/openapi.json\n";
            $parts[] = "```json";
            $parts[] = self::json($endpoints);
            $parts[] = "```\n";
        }

        // 5. Certification status
        $parts[] = "## 5. Certification status (this site)\n";
        if (empty($config['certification'])) {
            $parts[] = "This site is not currently in any public certification directory.\n";
        } else {
            $parts[] = "```json";
            $parts[] = self::json($config['certification']);
            $parts[] = "```\n";
        }

        // 6. Content licensing
        $parts[] = "## 6. Content licensing for AI training\n";
        $licensing = $config['licensing'] ?? [];
        if (is_array($licensing) && !empty($licensing)) {
            foreach ($licensing as $line) $parts[] = "- " . $line;
            $parts[] = "";
        } else {
            $parts[] = "No explicit licensing declared. Agents SHOULD NOT assume permission to use content for training or redistribution.\n";
        }

        // 7. Page index
        $parts[] = "## 7. Page index\n";
        foreach (($config['pages'] ?? []) as $p) {
            $url = $p['url'] ?? '/';
            $title = $p['title'] ?? $url;
            $fullUrl = (str_starts_with($url, 'http')) ? $url : $siteUrl . $url;
            $parts[] = "- [{$title}]({$fullUrl})";
        }
        $parts[] = "";

        // 8. Fallback files
        $parts[] = "## 8. Fallback — individual discovery files\n";
        $parts[] = "If an agent cannot parse this unified manifest, fall back to the following files at this site's root.";
        $parts[] = "**If this manifest disagrees with any of them, this manifest is authoritative.**\n";
        $parts[] = "| File | Purpose |";
        $parts[] = "|---|---|";
        $fallback = $config['fallback_files'] ?? self::defaultFallback();
        foreach ($fallback as $f) {
            $parts[] = "| `" . ($f['path'] ?? '?') . "` | " . ($f['purpose'] ?? '') . " |";
        }
        $parts[] = "";

        // 9. Spec metadata
        $parts[] = "## 9. Spec metadata\n";
        $parts[] = "```json";
        $parts[] = self::json([
            'spec'                    => 'Bippsi AI Standard',
            'spec_version'            => '1.0',
            'spec_url'                => 'https://github.com/bigappstudiollc/bippsi/blob/main/ai-standard/SPEC.md',
            'spec_source'             => 'https://bippsi.com/bippsi-standard',
            'manifest_generated_at'   => $now,
            'manifest_version'        => '1.0',
            'publisher'               => $config['identity']['name'] ?? '',
        ]);
        $parts[] = "```\n";

        $parts[] = "---\n";
        $parts[] = "*This manifest follows the Bippsi AI Standard v1.0. Spec: <https://github.com/bigappstudiollc/bippsi>.*\n";

        return implode("\n", $parts);
    }

    /** Pretty-print JSON the way the spec expects inside fenced code blocks. */
    private static function json($data): string {
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /** Parse an OpenAPI 3.x spec and return a compact endpoint list. */
    private static function loadOpenApi(string $path): array {
        if (!is_readable($path)) return [];
        $spec = json_decode((string)file_get_contents($path), true);
        if (!is_array($spec)) return [];
        $base = rtrim(($spec['servers'][0]['url'] ?? ''), '/');
        $out = [];
        foreach (($spec['paths'] ?? []) as $p => $ops) {
            foreach ($ops as $method => $op) {
                if (!in_array(strtolower($method), ['get','post','put','patch','delete'], true)) continue;
                $out[] = [
                    'method'  => strtoupper($method),
                    'url'     => $base . $p,
                    'summary' => $op['summary'] ?? '',
                    'auth'    => !empty($op['security']) ? 'Bearer required' : 'none',
                ];
            }
        }
        return $out;
    }

    /** Default fallback-files table per spec v1.0 section 8. */
    private static function defaultFallback(): array {
        return [
            ['path' => '/AGENTS.md',                 'purpose' => 'Narrative agent policy'],
            ['path' => '/llms.txt',                  'purpose' => 'LLM-friendly content index'],
            ['path' => '/agents.json',               'purpose' => 'Structured agent policy'],
            ['path' => '/openapi.json',              'purpose' => 'REST API spec (OpenAPI 3.1)'],
            ['path' => '/.well-known/mcp.json',      'purpose' => 'MCP server manifest'],
            ['path' => '/.well-known/ai-plugin.json','purpose' => 'OpenAI plugin manifest'],
            ['path' => '/.well-known/security.txt',  'purpose' => 'Security disclosure (RFC 9116)'],
            ['path' => '/manifest.json',             'purpose' => 'PWA manifest'],
            ['path' => '/robots.txt',                'purpose' => 'Crawler policy'],
            ['path' => '/sitemap.xml',               'purpose' => 'URL index'],
        ];
    }
}

// ── CLI mode ───────────────────────────────────────────────────────────────────
if (PHP_SAPI === 'cli' && isset($argv[1])) {
    $configPath = $argv[1];
    if (!is_readable($configPath)) {
        fwrite(STDERR, "Cannot read config file: {$configPath}\n");
        exit(1);
    }
    $config = json_decode((string)file_get_contents($configPath), true);
    if (!is_array($config)) {
        fwrite(STDERR, "Config file is not valid JSON: {$configPath}\n");
        exit(1);
    }
    echo BippsiUnified::render($config);
    exit(0);
}
