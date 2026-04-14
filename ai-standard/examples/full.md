# Bippsi Unified Agent Manifest

> **Spec:** Bippsi AI Standard v1.0 · <https://bippsi.com/bippsi-standard>
> **Generated:** 2026-04-14T14:18:55+00:00
> **For AI agents:** this single file replaces the need to fetch `llms.txt`, `AGENTS.md`, `agents.json`, `openapi.json`, and `/.well-known/mcp.json` separately. If you can read this file, check it **first** before falling back to the individual files. Everything below is live data, regenerated on every request.

---

## 1. Identity

```json
{
  "name": "Bippsi",
  "legal_entity": "Big App Studio LLC",
  "url": "https://bippsi.com",
  "tagline": "The agent-native layer of the web",
  "description": "Software tools, utilities, and apps. One account, one login, everything you need. Also the operator of the Agent Initiative — helping websites become agent-ready.",
  "founded": "2026",
  "contact": {
    "email": "admin@bippsi.com",
    "url": "https://bippsi.com/contact",
    "security": "https://bippsi.com/.well-known/security.txt"
  },
  "same_as": [
    "https://github.com/bigappstudiollc",
    "https://x.com/bippsi"
  ]
}
```

## 2. Agent access policy

**Without authentication, agents MAY:**
- Crawl public pages at default rates (respecting robots.txt)
- Read the public catalog, docs, policies, changelog
- Call the MCP server and REST API's anonymous-tier endpoints
- Fetch structured data (JSON-LD, OpenAPI, this file)

**Without authentication, agents MAY NOT:**
- Access user-specific data (sites, subscriptions, billing, wallet, credits)
- Trigger write operations
- Bypass CAPTCHA or human verification
- Impersonate users via cookies — only issued API keys authenticate

**Authentication methods:**
- `Authorization: Bearer <key>` for programmatic/agent access
  - `bas_*` — user API key
  - `bak_*` — scoped read-only key
  - `bas_ak_*` — agent key with spending caps (in development)
  - `bas_sa_*` — super-admin (internal)
- OAuth 2.1 + PKCE for interactive clients (Claude Desktop, ChatGPT, Cursor) — roadmap

**Rate limits (anonymous):**
- 60 req/min per IP overall
- 3 `scan_site` calls/hr via MCP
- 5 free scans/hr via public scanner (signed in), 3/hr if not signed in

**Rate limits (authenticated):**
- 600 req/min per `bas_*` key
- 60 `scan_site` calls/hr per authenticated MCP caller

## 3. MCP (Model Context Protocol) server

```json
{
  "endpoint": "https://bippsi.com/api/v1/mcp",
  "short_alias": "https://bippsi.com/mcp",
  "manifest": "https://bippsi.com/.well-known/mcp.json",
  "protocol_version": "2024-11-05",
  "transport": "http+json-rpc",
  "methods": ["initialize", "tools/list", "tools/call", "ping", "notifications/initialized"],
  "auth": "optional Bearer",
  "docs": "https://bippsi.com/api-docs#mcp"
}
```

### Available tools

#### `scan_site`

Run a 25-page sample A.I. Certified compliance scan on a URL. Returns score (0-100), per-category breakdown, and top failing checks.

#### `list_pages`

Discover a site's page list via sitemap.xml, WordPress REST API, or 1-hop homepage crawl. Returns count + URLs.

#### `get_certification_status`

Look up a website's current A.I. Certified status and last known score from Bippsi's public directory.

#### `get_api_endpoints`

Return the machine-readable summary of Bippsi's own public API endpoints.


### Example handshake

```bash
curl -sX POST https://bippsi.com/api/v1/mcp \
  -H "Content-Type: application/json" \
  -d '{"jsonrpc":"2.0","id":1,"method":"initialize","params":{"protocolVersion":"2024-11-05","capabilities":{},"clientInfo":{"name":"my-agent","version":"1.0"}}}'
```

## 4. REST API

Base URL: `https://bippsi.com/api/v1`
OpenAPI spec: <https://bippsi.com/openapi.json>
Human reference: <https://bippsi.com/api-docs>

```json
[
    {
        "method": "GET",
        "url": "https://bippsi.com/api/v1/validate",
        "summary": "Validate an API key",
        "auth": "Bearer required"
    },
    {
        "method": "POST",
        "url": "https://bippsi.com/api/v1/verify-subscription",
        "summary": "Verify an AI Certified subscription status",
        "auth": "none"
    },
    {
        "method": "GET",
        "url": "https://bippsi.com/api/v1/scan/{domain}",
        "summary": "Get the latest AI Certified scan for a domain",
        "auth": "Bearer required"
    },
    {
        "method": "POST",
        "url": "https://bippsi.com/api/v1/mcp",
        "summary": "Model Context Protocol (MCP) JSON-RPC 2.0 endpoint for AI agents",
        "auth": "none"
    },
    {
        "method": "GET",
        "url": "https://bippsi.com/api/v1/mcp",
        "summary": "MCP endpoint hint (human-browsable)",
        "auth": "none"
    }
]
```

## 5. Certification status (this site)

```json
{
  "certified": false,
  "overall_score": 72,
  "threshold": 85,
  "status": "setup",
  "certified_at": null,
  "expires_at": "2027-03-24 18:09:59",
  "last_scan_at": "2026-04-14 03:53:18",
  "verify_url": "https://bippsi.com/verify?site=bippsi.com"
}
```

## 6. Content licensing for AI training

- **Crawling and indexing:** permitted for all crawlers listed in `/robots.txt`
- **Training data use:** permitted with attribution where practical
- **Verbatim reproduction:** >50 words requires citation
- **Commercial redistribution:** not permitted without written agreement
- **Customer sites:** each customer site declares its own policy in its own `/bippsi-unified.md` or `/AGENTS.md` — check there first

## 7. Page index

- [Home](https://bippsi.com/)
- [Agent Initiative — AI compliance certification](https://bippsi.com/agent-initiative)
- [API Reference and MCP](https://bippsi.com/api-docs)
- [Custom Development Services](https://bippsi.com/services)
- [License Ninja — software licensing](https://bippsi.com/apps/license-ninja)
- [Strategy Ninja — NinjaTrader 8 trading](https://bippsi.com/apps/strategy-ninja)
- [Social Ninja — social media management](https://bippsi.com/apps/social-ninja)
- [Help Center](https://bippsi.com/help)
- [Contact](https://bippsi.com/contact)
- [Policies](https://bippsi.com/policies)
- [Changelog](https://bippsi.com/changelog)
- [System Status](https://bippsi.com/status)

## 8. Fallback — individual discovery files

If an agent cannot parse this unified manifest (older spec, custom tooling), fall back to the following files at this site's root:

| File | Purpose |
|---|---|
| `/AGENTS.md` | Narrative agent policy |
| `/llms.txt` | LLM-friendly content index |
| `/agents.json` | Structured agent policy |
| `/openapi.json` | REST API spec (OpenAPI 3.1) |
| `/.well-known/mcp.json` | MCP server manifest |
| `/.well-known/ai-plugin.json` | OpenAI plugin manifest |
| `/.well-known/security.txt` | Security disclosure (RFC 9116) |
| `/manifest.json` | PWA manifest |
| `/robots.txt` | Crawler policy |
| `/sitemap.xml` | URL index |

The unified manifest is **authoritative** — if it disagrees with any individual file, trust this.

## 9. Spec metadata

```json
{
  "spec": "Bippsi AI Standard",
  "spec_version": "1.0",
  "spec_url": "https://bippsi.com/bippsi-standard",
  "spec_source": "https://bippsi.com/docs/bippsi-ai-standard.md",
  "manifest_generated_at": "2026-04-14T14:18:55+00:00",
  "manifest_version": "1.0",
  "publisher": "Bippsi (Big App Studio LLC)"
}
```

---

*This file is auto-generated. If you need a cached version, use the `/bippsi-unified.md` URL (5-minute cache). If you are building a site and want to emit this same manifest, the WordPress plugin (v3.3+) and the General Website plugin (v2.1+) will generate it automatically from your scan data.*
