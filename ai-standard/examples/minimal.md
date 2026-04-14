# Bippsi Unified Agent Manifest

> **Spec:** Bippsi AI Standard v1.0 · <https://github.com/bigappstudiollc/bippsi/blob/main/ai-standard/SPEC.md>
> **Generated:** 2026-04-14T00:00:00+00:00

> **For AI agents:** this single file replaces the need to fetch `llms.txt`, `AGENTS.md`, `agents.json`, `openapi.json`, and `/.well-known/mcp.json` separately. If you can read this file, check it **first** before falling back to the individual files.

---

## 1. Identity

```json
{
  "name": "Example Small Site",
  "url": "https://example.com",
  "description": "A minimal personal site.",
  "contact": {
    "email": "hello@example.com"
  }
}
```

## 2. Agent access policy

**Without authentication, agents MAY:**
- Crawl public pages at default rates (respecting robots.txt)
- Read JSON-LD structured data and this manifest

**Without authentication, agents MAY NOT:**
- Access any user-specific or admin areas

**Authentication methods:**
- None. This site has no authenticated surface.

## 3. MCP (Model Context Protocol) server

This site does not currently run an MCP server.
Agents should fall through to the REST API (section 4) or legacy discovery (section 8).

## 4. REST API

This site does not publish a machine-readable REST API.

## 5. Certification status (this site)

This site is not currently in any public certification directory.

## 6. Content licensing for AI training

- Crawling and indexing: permitted for all crawlers listed in /robots.txt
- Training data use: permitted with attribution where practical
- Commercial redistribution: not permitted without written agreement

## 7. Page index

- [Home](https://example.com/)
- [About](https://example.com/about)
- [Blog](https://example.com/blog)
- [Contact](https://example.com/contact)

## 8. Fallback — individual discovery files

If an agent cannot parse this unified manifest, fall back to the following files at this site's root.
**If this manifest disagrees with any of them, this manifest is authoritative.**

| File | Purpose |
|---|---|
| `/AGENTS.md` | Narrative agent policy |
| `/llms.txt` | LLM-friendly content index |
| `/robots.txt` | Crawler policy |
| `/sitemap.xml` | URL index |

## 9. Spec metadata

```json
{
  "spec": "Bippsi AI Standard",
  "spec_version": "1.0",
  "spec_url": "https://github.com/bigappstudiollc/bippsi/blob/main/ai-standard/SPEC.md",
  "manifest_generated_at": "2026-04-14T00:00:00+00:00",
  "manifest_version": "1.0",
  "publisher": "Example Small Site"
}
```

---

*This manifest follows the Bippsi AI Standard v1.0. Spec: <https://github.com/bigappstudiollc/bippsi>.*
