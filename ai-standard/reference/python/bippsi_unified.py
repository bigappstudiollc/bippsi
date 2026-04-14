#!/usr/bin/env python3
"""
Bippsi AI Standard — reference generator (Python)

Produces a conforming /bippsi-unified.md markdown document from a configuration dict.
Dependency-free: requires only Python 3.10+ standard library. No pip install.

Two ways to use:

  1. IMPORT + CALL (dynamic, use in Flask/FastAPI/Django handler):

       from bippsi_unified import render_bippsi_unified

       @app.get("/bippsi-unified.md")
       def unified():
           return Response(render_bippsi_unified(config), media_type="text/markdown")

  2. CLI GENERATION (static):

       python bippsi_unified.py config.json > public/bippsi-unified.md

See SPEC at ../../SPEC.md or
https://github.com/bigappstudiollc/bippsi/blob/main/ai-standard/SPEC.md

License: MIT (see ../../../LICENSE-CODE)
"""

from __future__ import annotations

import json
import sys
from datetime import datetime, timezone
from pathlib import Path
from typing import Any


def render_bippsi_unified(config: dict[str, Any]) -> str:
    """Render a conforming /bippsi-unified.md from a configuration dict.

    Required top-level keys:
      site_url        : 'https://example.com'
      identity        : dict with name, tagline, description, contact, same_as
      agent_policy    : dict with allowed, denied, auth_methods, rate_limits
      mcp             : None | dict with endpoint, protocol_version, transport, tools
      api             : None | str path to openapi.json | list of endpoint dicts
      certification   : None | dict
      licensing       : list of bullet strings
      pages           : list of dicts with url + title
      fallback_files  : list of dicts with path + purpose (optional; defaults to spec v1.0 list)

    See examples/full.md in this repo for a complete config example.
    """
    site_url = (config.get("site_url") or "https://example.com").rstrip("/")
    now = datetime.now(timezone.utc).isoformat(timespec="seconds")
    parts: list[str] = []

    # Header
    parts.append("# Bippsi Unified Agent Manifest\n")
    parts.append("> **Spec:** Bippsi AI Standard v1.0 · <https://github.com/bigappstudiollc/bippsi/blob/main/ai-standard/SPEC.md>")
    parts.append(f"> **Generated:** {now}\n")
    parts.append(
        "> **For AI agents:** this single file replaces the need to fetch `llms.txt`, `AGENTS.md`, "
        "`agents.json`, `openapi.json`, and `/.well-known/mcp.json` separately. "
        "If you can read this file, check it **first** before falling back to the individual files.\n"
    )
    parts.append("---\n")

    # 1. Identity
    parts.append("## 1. Identity\n")
    parts.append("```json")
    parts.append(_json(config.get("identity", {})))
    parts.append("```\n")

    # 2. Agent access policy
    policy = config.get("agent_policy") or {}
    parts.append("## 2. Agent access policy\n")
    if policy.get("allowed"):
        parts.append("**Without authentication, agents MAY:**")
        for item in policy["allowed"]:
            parts.append(f"- {item}")
        parts.append("")
    if policy.get("denied"):
        parts.append("**Without authentication, agents MAY NOT:**")
        for item in policy["denied"]:
            parts.append(f"- {item}")
        parts.append("")
    if policy.get("auth_methods"):
        parts.append("**Authentication methods:**")
        for item in policy["auth_methods"]:
            parts.append(f"- {item}")
        parts.append("")
    if policy.get("rate_limits"):
        parts.append("**Rate limits:**")
        parts.append("```json")
        parts.append(_json(policy["rate_limits"]))
        parts.append("```\n")

    # 3. MCP server
    parts.append("## 3. MCP (Model Context Protocol) server\n")
    if not config.get("mcp"):
        parts.append("This site does not currently run an MCP server.")
        parts.append("Agents should fall through to the REST API (section 4) or legacy discovery (section 8).\n")
    else:
        parts.append("```json")
        parts.append(_json(config["mcp"]))
        parts.append("```\n")
        tools = config["mcp"].get("tools") or []
        if tools:
            parts.append("### Available tools\n")
            for tool in tools:
                parts.append(f"#### `{tool.get('name', '?')}`\n")
                parts.append(f"{(tool.get('description') or '').strip()}\n")

    # 4. REST API
    parts.append("## 4. REST API\n")
    api = config.get("api")
    if not api:
        parts.append("This site does not publish a machine-readable REST API.\n")
    else:
        endpoints = _load_openapi(api) if isinstance(api, str) else api
        parts.append(f"OpenAPI spec: {site_url}/openapi.json\n")
        parts.append("```json")
        parts.append(_json(endpoints))
        parts.append("```\n")

    # 5. Certification
    parts.append("## 5. Certification status (this site)\n")
    if not config.get("certification"):
        parts.append("This site is not currently in any public certification directory.\n")
    else:
        parts.append("```json")
        parts.append(_json(config["certification"]))
        parts.append("```\n")

    # 6. Licensing
    parts.append("## 6. Content licensing for AI training\n")
    licensing = config.get("licensing") or []
    if licensing:
        for line in licensing:
            parts.append(f"- {line}")
        parts.append("")
    else:
        parts.append("No explicit licensing declared. Agents SHOULD NOT assume permission to use content for training or redistribution.\n")

    # 7. Pages
    parts.append("## 7. Page index\n")
    for page in config.get("pages") or []:
        url = page.get("url", "/")
        full = url if url.startswith("http") else f"{site_url}{url}"
        parts.append(f"- [{page.get('title', url)}]({full})")
    parts.append("")

    # 8. Fallback files
    parts.append("## 8. Fallback — individual discovery files\n")
    parts.append("If an agent cannot parse this unified manifest, fall back to the following files at this site's root.")
    parts.append("**If this manifest disagrees with any of them, this manifest is authoritative.**\n")
    parts.append("| File | Purpose |")
    parts.append("|---|---|")
    for f in (config.get("fallback_files") or _default_fallback()):
        parts.append(f"| `{f.get('path', '?')}` | {f.get('purpose', '')} |")
    parts.append("")

    # 9. Spec metadata
    parts.append("## 9. Spec metadata\n")
    parts.append("```json")
    parts.append(_json({
        "spec": "Bippsi AI Standard",
        "spec_version": "1.0",
        "spec_url": "https://github.com/bigappstudiollc/bippsi/blob/main/ai-standard/SPEC.md",
        "spec_source": "https://bippsi.com/bippsi-standard",
        "manifest_generated_at": now,
        "manifest_version": "1.0",
        "publisher": (config.get("identity") or {}).get("name", ""),
    }))
    parts.append("```\n")

    parts.append("---\n")
    parts.append("*This manifest follows the Bippsi AI Standard v1.0. Spec: <https://github.com/bigappstudiollc/bippsi>.*\n")

    return "\n".join(parts)


def _json(data: Any) -> str:
    return json.dumps(data, indent=2, ensure_ascii=False)


def _load_openapi(path: str) -> list[dict[str, str]]:
    """Parse an OpenAPI 3.x spec file and return a compact endpoint list."""
    try:
        spec = json.loads(Path(path).read_text(encoding="utf-8"))
    except (OSError, json.JSONDecodeError):
        return []
    base = (spec.get("servers") or [{}])[0].get("url", "").rstrip("/")
    out = []
    for p, ops in (spec.get("paths") or {}).items():
        for method, op in ops.items():
            if method.lower() not in {"get", "post", "put", "patch", "delete"}:
                continue
            out.append({
                "method":  method.upper(),
                "url":     base + p,
                "summary": op.get("summary", ""),
                "auth":    "Bearer required" if op.get("security") else "none",
            })
    return out


def _default_fallback() -> list[dict[str, str]]:
    """Default fallback-files table per spec v1.0 section 8."""
    return [
        {"path": "/AGENTS.md",                  "purpose": "Narrative agent policy"},
        {"path": "/llms.txt",                   "purpose": "LLM-friendly content index"},
        {"path": "/agents.json",                "purpose": "Structured agent policy"},
        {"path": "/openapi.json",               "purpose": "REST API spec (OpenAPI 3.1)"},
        {"path": "/.well-known/mcp.json",       "purpose": "MCP server manifest"},
        {"path": "/.well-known/ai-plugin.json", "purpose": "OpenAI plugin manifest"},
        {"path": "/.well-known/security.txt",   "purpose": "Security disclosure (RFC 9116)"},
        {"path": "/manifest.json",              "purpose": "PWA manifest"},
        {"path": "/robots.txt",                 "purpose": "Crawler policy"},
        {"path": "/sitemap.xml",                "purpose": "URL index"},
    ]


# ── CLI mode ──────────────────────────────────────────────────────────────────
if __name__ == "__main__":
    if len(sys.argv) < 2:
        sys.stderr.write("Usage: python bippsi_unified.py <config.json> > output.md\n")
        sys.exit(1)
    try:
        cfg = json.loads(Path(sys.argv[1]).read_text(encoding="utf-8"))
    except (OSError, json.JSONDecodeError) as e:
        sys.stderr.write(f"Error reading config: {e}\n")
        sys.exit(1)
    sys.stdout.write(render_bippsi_unified(cfg))
