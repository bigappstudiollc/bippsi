#!/usr/bin/env node
/**
 * Bippsi AI Standard — reference generator (Node.js)
 *
 * Produces a conforming /bippsi-unified.md markdown document from a configuration object.
 * Dependency-free: requires only Node.js 20+ standard library. No npm install.
 *
 * Two ways to use:
 *
 *   1. IMPORT + CALL (dynamic, use in an Express/Fastify/Hono handler):
 *
 *        import { renderBippsiUnified } from './bippsi-unified.js';
 *        app.get('/bippsi-unified.md', (req, res) => {
 *          res.type('text/markdown').send(renderBippsiUnified(config));
 *        });
 *
 *   2. CLI GENERATION (static):
 *
 *        node bippsi-unified.js config.json > public/bippsi-unified.md
 *
 * See SPEC at ../../SPEC.md or https://github.com/bigappstudiollc/bippsi/blob/main/ai-standard/SPEC.md
 *
 * License: MIT (see ../../../LICENSE-CODE)
 */

import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { argv } from 'node:process';

/**
 * Render a conforming /bippsi-unified.md from a configuration object.
 *
 * Required top-level keys:
 *   site_url         : 'https://example.com'
 *   identity         : { name, tagline, description, contact: { email, url }, same_as: [] }
 *   agent_policy     : { allowed: [], denied: [], auth_methods: [], rate_limits: {} }
 *   mcp              : null | { endpoint, protocol_version, transport, tools: [{name, description}] }
 *   api              : null | path/to/openapi.json | array of endpoint objects
 *   certification    : null | { certified, overall_score, status, verify_url, ... }
 *   licensing        : [line, line, ...]  (bullet strings)
 *   pages            : [{ url, title }]
 *   fallback_files   : [{ path, purpose }]
 *
 * See examples/full.md in this repo for a complete config.
 */
export function renderBippsiUnified(config) {
  const siteUrl = (config.site_url || 'https://example.com').replace(/\/+$/, '');
  const now = new Date().toISOString();
  const out = [];

  // Header
  out.push('# Bippsi Unified Agent Manifest\n');
  out.push('> **Spec:** Bippsi AI Standard v1.0 · <https://github.com/bigappstudiollc/bippsi/blob/main/ai-standard/SPEC.md>');
  out.push(`> **Generated:** ${now}\n`);
  out.push('> **For AI agents:** this single file replaces the need to fetch `llms.txt`, `AGENTS.md`, `agents.json`, `openapi.json`, and `/.well-known/mcp.json` separately. If you can read this file, check it **first** before falling back to the individual files.\n');
  out.push('---\n');

  // 1. Identity
  out.push('## 1. Identity\n');
  out.push('```json');
  out.push(jsonBlock(config.identity || {}));
  out.push('```\n');

  // 2. Agent access policy
  const p = config.agent_policy || {};
  out.push('## 2. Agent access policy\n');
  if (p.allowed?.length) {
    out.push('**Without authentication, agents MAY:**');
    p.allowed.forEach(i => out.push(`- ${i}`));
    out.push('');
  }
  if (p.denied?.length) {
    out.push('**Without authentication, agents MAY NOT:**');
    p.denied.forEach(i => out.push(`- ${i}`));
    out.push('');
  }
  if (p.auth_methods?.length) {
    out.push('**Authentication methods:**');
    p.auth_methods.forEach(i => out.push(`- ${i}`));
    out.push('');
  }
  if (p.rate_limits) {
    out.push('**Rate limits:**');
    out.push('```json');
    out.push(jsonBlock(p.rate_limits));
    out.push('```\n');
  }

  // 3. MCP server
  out.push('## 3. MCP (Model Context Protocol) server\n');
  if (!config.mcp) {
    out.push('This site does not currently run an MCP server.');
    out.push('Agents should fall through to the REST API (section 4) or legacy discovery (section 8).\n');
  } else {
    out.push('```json');
    out.push(jsonBlock(config.mcp));
    out.push('```\n');
    if (config.mcp.tools?.length) {
      out.push('### Available tools\n');
      for (const t of config.mcp.tools) {
        out.push(`#### \`${t.name || '?'}\`\n`);
        out.push(`${(t.description || '').trim()}\n`);
      }
    }
  }

  // 4. REST API
  out.push('## 4. REST API\n');
  if (!config.api) {
    out.push('This site does not publish a machine-readable REST API.\n');
  } else {
    const endpoints = typeof config.api === 'string' ? loadOpenApi(config.api) : config.api;
    out.push(`OpenAPI spec: ${siteUrl}/openapi.json\n`);
    out.push('```json');
    out.push(jsonBlock(endpoints));
    out.push('```\n');
  }

  // 5. Certification
  out.push('## 5. Certification status (this site)\n');
  if (!config.certification) {
    out.push('This site is not currently in any public certification directory.\n');
  } else {
    out.push('```json');
    out.push(jsonBlock(config.certification));
    out.push('```\n');
  }

  // 6. Licensing
  out.push('## 6. Content licensing for AI training\n');
  if (Array.isArray(config.licensing) && config.licensing.length) {
    config.licensing.forEach(l => out.push(`- ${l}`));
    out.push('');
  } else {
    out.push('No explicit licensing declared. Agents SHOULD NOT assume permission to use content for training or redistribution.\n');
  }

  // 7. Pages
  out.push('## 7. Page index\n');
  for (const pg of (config.pages || [])) {
    const url = pg.url || '/';
    const full = url.startsWith('http') ? url : `${siteUrl}${url}`;
    out.push(`- [${pg.title || url}](${full})`);
  }
  out.push('');

  // 8. Fallback files
  out.push('## 8. Fallback — individual discovery files\n');
  out.push("If an agent cannot parse this unified manifest, fall back to the following files at this site's root.");
  out.push('**If this manifest disagrees with any of them, this manifest is authoritative.**\n');
  out.push('| File | Purpose |');
  out.push('|---|---|');
  const fallback = config.fallback_files || defaultFallback();
  for (const f of fallback) out.push(`| \`${f.path || '?'}\` | ${f.purpose || ''} |`);
  out.push('');

  // 9. Spec metadata
  out.push('## 9. Spec metadata\n');
  out.push('```json');
  out.push(jsonBlock({
    spec: 'Bippsi AI Standard',
    spec_version: '1.0',
    spec_url: 'https://github.com/bigappstudiollc/bippsi/blob/main/ai-standard/SPEC.md',
    spec_source: 'https://bippsi.com/bippsi-standard',
    manifest_generated_at: now,
    manifest_version: '1.0',
    publisher: config.identity?.name || '',
  }));
  out.push('```\n');

  out.push('---\n');
  out.push('*This manifest follows the Bippsi AI Standard v1.0. Spec: <https://github.com/bigappstudiollc/bippsi>.*\n');

  return out.join('\n');
}

function jsonBlock(data) {
  return JSON.stringify(data, null, 2);
}

function loadOpenApi(path) {
  try {
    const spec = JSON.parse(readFileSync(path, 'utf8'));
    const base = (spec.servers?.[0]?.url || '').replace(/\/+$/, '');
    const out = [];
    for (const [p, ops] of Object.entries(spec.paths || {})) {
      for (const [method, op] of Object.entries(ops)) {
        if (!['get','post','put','patch','delete'].includes(method.toLowerCase())) continue;
        out.push({
          method:  method.toUpperCase(),
          url:     base + p,
          summary: op.summary || '',
          auth:    op.security?.length ? 'Bearer required' : 'none',
        });
      }
    }
    return out;
  } catch {
    return [];
  }
}

function defaultFallback() {
  return [
    { path: '/AGENTS.md',                  purpose: 'Narrative agent policy' },
    { path: '/llms.txt',                   purpose: 'LLM-friendly content index' },
    { path: '/agents.json',                purpose: 'Structured agent policy' },
    { path: '/openapi.json',               purpose: 'REST API spec (OpenAPI 3.1)' },
    { path: '/.well-known/mcp.json',       purpose: 'MCP server manifest' },
    { path: '/.well-known/ai-plugin.json', purpose: 'OpenAI plugin manifest' },
    { path: '/.well-known/security.txt',   purpose: 'Security disclosure (RFC 9116)' },
    { path: '/manifest.json',              purpose: 'PWA manifest' },
    { path: '/robots.txt',                 purpose: 'Crawler policy' },
    { path: '/sitemap.xml',                purpose: 'URL index' },
  ];
}

// ── CLI mode ──────────────────────────────────────────────────────────────────
// Run when this file is invoked directly: `node bippsi-unified.js config.json`
if (import.meta.url === `file://${process.argv[1].replace(/\\/g, '/')}`) {
  const configPath = argv[2];
  if (!configPath) {
    console.error('Usage: node bippsi-unified.js <config.json> > output.md');
    process.exit(1);
  }
  try {
    const config = JSON.parse(readFileSync(configPath, 'utf8'));
    process.stdout.write(renderBippsiUnified(config));
  } catch (err) {
    console.error(`Error: ${err.message}`);
    process.exit(1);
  }
}
