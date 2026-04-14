# Bippsi AI Standard — v1.0

**Authors:** Bippsi / Big App Studio LLC
**Status:** Living standard. Version 1.0 frozen 2026-04-14.
**Canonical URL:** https://bippsi.com/bippsi-standard
**Machine URL:** https://bippsi.com/bippsi-unified.md

---

## Why this exists

The web already has ad-hoc conventions for making sites AI-readable — `/robots.txt`, `/sitemap.xml`, `/.well-known/security.txt` (RFC 9116, real), `/llms.txt` (de facto), `/AGENTS.md` (de facto), `/openapi.json` (community), `/.well-known/ai-plugin.json` (OpenAI). No single body has standardized how AI agents *discover* a site and learn what they can do there.

An agent operating at scale currently has to fetch up to **ten files** per new domain just to answer "can I interact with this site, and how?" That's ten round-trips, ten parse paths, and ten chances to miss something.

The Bippsi AI Standard defines two layers:

1. **The broken system** — the existing individual files. Every site should still publish them for backward compatibility with older agents.
2. **The unified system** — a single file, `/bippsi-unified.md`, that contains everything an agent needs. Modern agents check this first. If it exists, the agent has all the data in one round-trip.

Sites that publish the unified manifest are **preferred** by Bippsi-aware agents and score bonus points on the A.I. Certified scanner.

---

## The broken system (legacy discovery)

### Required files (unchanged from existing conventions)

Every AI-ready site should publish:

| Path | Purpose | Format | Real standard? |
|---|---|---|---|
| `/robots.txt` | Crawler policy, AI bot allowlist | Plain text | ✅ RFC 9309 |
| `/sitemap.xml` | URL index | XML | ✅ sitemaps.org |
| `/.well-known/security.txt` | Vulnerability disclosure contact | RFC 9116 | ✅ |
| `/llms.txt` | LLM-friendly content index | Markdown | De facto (Answer.AI) |
| `/AGENTS.md` | Narrative agent policy | Markdown | De facto |
| `/agents.json` | Structured agent policy | JSON | Proposed |
| `/openapi.json` | REST API spec | OpenAPI 3.x | ✅ community |
| `/.well-known/ai-plugin.json` | OpenAI plugin manifest | JSON | OpenAI vendor-specific |
| `/.well-known/mcp.json` | MCP server manifest | JSON | **Proposed by this spec** |
| `/manifest.json` | PWA manifest | W3C | ✅ |

### Bippsi's proposed MCP discovery location

The [Model Context Protocol](https://modelcontextprotocol.io) spec itself standardizes the *protocol* but does **not** define a remote-server discovery URL. Until it does, Bippsi proposes:

> **Convention:** A site running an MCP server SHOULD publish a JSON manifest at `/.well-known/mcp.json` describing its endpoint, protocol version, capabilities, and tools. Agents SHOULD check this path first before attempting legacy `/mcp` or subdomain-based discovery.

Manifest schema (minimum):

```json
{
  "name": "string",
  "protocol_version": "2024-11-05",
  "transport": "http+json-rpc",
  "endpoint": "https://example.com/api/v1/mcp",
  "capabilities": { "tools": true, "resources": false, "prompts": false },
  "tools": [{ "name": "string", "description": "string" }]
}
```

### Minimum legacy requirements for A.I. Certified

To pass the A.I. Certified threshold on the legacy system alone:

- **Required (failing any of these drops the site below 85%):**
  - `/robots.txt` with at least one AI bot explicitly allowed
  - `/sitemap.xml`
  - `/llms.txt`
  - `/AGENTS.md`
  - JSON-LD Organization schema on homepage
  - JSON-LD WebPage schema on every page
  - `/openapi.json` OR a `/docs`-style API reference (even a stub)

- **Strongly recommended (tier 2 signals):**
  - `/.well-known/security.txt`
  - `/.well-known/ai-plugin.json`
  - `/agents.json` with structured policies
  - MCP manifest at `/.well-known/mcp.json` even if pointing to a minimal endpoint
  - Per-bot rules in `/robots.txt` (GPTBot, ClaudeBot, etc., not just `User-agent: *`)

---

## The unified system

### Location

**`/bippsi-unified.md`** at the site root. Plain markdown, served as `Content-Type: text/markdown`.

The `.md` extension is intentional: agents can render it as human-readable markdown AND parse the fenced JSON blocks inside it with any standard parser. No new tooling required.

### Why at the root and not `.well-known`

`.well-known/` is for *machine-only* well-known URIs (RFC 8615). The unified manifest is designed to be **both human-readable and machine-parseable** — it documents the site's agent capabilities in a way a developer can read over coffee. Root placement makes it pair with `/AGENTS.md` and `/llms.txt` as the "top-level agent docs."

### Required sections (in order)

A conforming `/bippsi-unified.md` MUST contain these sections, each starting with the exact ATX heading shown:

1. `## 1. Identity` — Site identity as a JSON code block (name, url, description, contact, same_as)
2. `## 2. Agent access policy` — What agents may/may not do; auth methods; rate limits
3. `## 3. MCP (Model Context Protocol) server` — Endpoint, manifest, protocol version, tool list
4. `## 4. REST API` — Base URL, OpenAPI reference, endpoint list as JSON
5. `## 5. Certification status (this site)` — A.I. Certified score, status, verify URL
6. `## 6. Content licensing for AI training` — Training/reproduction/redistribution terms
7. `## 7. Page index` — Public pages list (equivalent to sitemap + llms.txt)
8. `## 8. Fallback — individual discovery files` — Pointers to the legacy files
9. `## 9. Spec metadata` — Spec version, generation timestamp, publisher

Optional sections (add after 9):
- `## 10. Agent payments` — 402 Payment Required flow, credit costs, settlement endpoints
- `## 11. Capabilities` — Custom tools or resources unique to this site
- `## 12. Changelog` — Recent updates to agent-relevant surfaces

### JSON blocks inside markdown

Each machine-parseable section SHOULD contain a fenced ` ```json ` block with the structured data. Agents can:
- Read the markdown linearly (LLMs handle this natively)
- Extract the JSON blocks via regex (`/```json\n(.*?)\n```/s`) for programmatic use

### Precedence

If `/bippsi-unified.md` exists AND disagrees with any individual file at the same site, **the unified manifest wins**. Publishers should regenerate it on every change (or auto-generate it from code — see below).

### Authoring

Sites have three options:

1. **Auto-generate** from code (Bippsi's approach — `bippsi-unified.php` regenerates on every request)
2. **Plugin-generate** — the Bippsi WordPress plugin (v3.3+) and General Website plugin (v2.1+) will emit this file automatically
3. **Hand-write** — sites with mostly-static content can maintain the file manually

Static manifests MUST include the `manifest_generated_at` timestamp so agents can detect staleness.

### Spec discovery from within the manifest

Section 9 (`Spec metadata`) MUST include:
```json
{
  "spec": "Bippsi AI Standard",
  "spec_version": "1.0",
  "spec_url": "https://bippsi.com/bippsi-standard",
  "spec_source": "https://bippsi.com/docs/bippsi-ai-standard.md"
}
```

This lets agents verify they're parsing a conforming manifest and look up the spec if they encounter unknown sections.

---

## Agent discovery algorithm

A conforming Bippsi-aware agent SHOULD follow this algorithm when first encountering a domain:

```
1. Fetch /bippsi-unified.md
   - If 200 and Content-Type is text/markdown (or text/plain): parse, done.
   - If 404 or wrong content-type: fall through to step 2.

2. Fetch /.well-known/mcp.json
   - If present, use it. The MCP server will have a tools/call route to query for more.

3. Fetch /AGENTS.md + /llms.txt + /openapi.json + /agents.json in parallel
   - Merge into an in-memory "unified view."

4. Fetch /.well-known/security.txt, /.well-known/ai-plugin.json, /robots.txt, /sitemap.xml on demand.

5. If none of the above exist, treat the site as "agent-unaware" and fall back to HTML scraping + schema.org extraction.
```

Time budget: step 1 SHOULD complete in a single round-trip for well-configured sites. Steps 2-4 add 3-4 round-trips. Step 5 is the traditional scraping path.

---

## How this interacts with A.I. Certified scoring

The A.I. Certified scanner (v2.0+) awards:

- **+1 check passed in `agent_native`** if `/bippsi-unified.md` exists and parses
- **+1 check passed in `ai_discovery`** if the manifest's `Spec metadata` declares `spec_version` ≥ 1.0
- **No penalty** for sites that don't publish the unified manifest — the legacy files are still the minimum requirement

Sites scoring 85%+ get the A.I. Certified badge. Sites that additionally publish a valid `/bippsi-unified.md` get a small badge modifier (proposed: a "Unified" dot on the certification badge) — exact visual TBD.

---

## Versioning

This spec uses semver. Breaking changes to required sections or the JSON schemas inside them bump the major version. Additive changes (new optional sections, new optional fields) bump the minor.

Version history:
- **1.0** — 2026-04-14. Initial release. Defines 9 required sections, proposes `/.well-known/mcp.json` as MCP discovery, pairs with A.I. Certified 2.0 scoring.

---

## Contact

- **Issues and proposals:** open an issue in the [bippsi repo](https://github.com/bigappstudiollc/bippsi/issues) using the "Spec proposal" template
- **Reference implementations:** see [`reference/`](./reference/) — PHP, Node, and Python ports, all dependency-free
- **Examples:** see [`examples/`](./examples/) — minimal and full conforming manifests
- **Email:** admin@bippsi.com for private or commercial questions

This is a living document. Contributions welcome — see [CONTRIBUTING.md](../CONTRIBUTING.md).
