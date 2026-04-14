# Bippsi AI Standard

**Current version:** v1.0 (2026-04-14)

A specification for how websites publish agent-discoverable capabilities. Two layers:

- **Legacy** — the individual files most AI-aware sites already publish: `robots.txt`, `llms.txt`, `AGENTS.md`, `openapi.json`, `/.well-known/mcp.json`, etc.
- **Unified** — a single `/bippsi-unified.md` file at the site root containing everything an agent needs. Replaces the ten-file discovery hunt with one round-trip.

## The one-sentence pitch

Publish a single markdown file at `/bippsi-unified.md` with 9 required sections. Agents fetch it once, get your identity, access policy, MCP endpoint, API surface, certification status, and page index — all in one parseable artifact.

## Contents of this directory

```
ai-standard/
├── README.md                    ← you are here
├── SPEC.md                      ← the specification (CC BY 4.0)
├── CHANGELOG.md                 ← spec version history
├── reference/                   ← generator implementations (MIT)
│   ├── php/
│   │   └── bippsi-unified.php   ← dependency-free PHP generator
│   ├── node/
│   │   └── bippsi-unified.js    ← dependency-free Node generator
│   └── python/
│       └── bippsi_unified.py    ← dependency-free Python generator
├── examples/
│   ├── minimal.md               ← smallest conforming manifest
│   └── full.md                  ← bippsi.com's live manifest as a worked example
└── schemas/                     ← JSON schemas for each section's data block (planned for v1.1)
```

## Quick start

### Implementing on your site

Pick a reference implementation for your stack:

- **PHP:** copy `reference/php/bippsi-unified.php` into your project, fill in the `$config` array at the top, route your webserver to serve it at `/bippsi-unified.md`.
- **Node:** `node reference/node/bippsi-unified.js path/to/your-config.json > public/bippsi-unified.md` at build time, or serve dynamically with any HTTP framework.
- **Python:** same pattern, `python reference/python/bippsi_unified.py your-config.json > public/bippsi-unified.md`.

All three are self-contained — **no dependencies**. Standard library only. Copy one file, fill in your config, you're done.

### Validating a manifest

For v1.0, validation is visual (render the markdown, confirm the 9 sections are present with the right headings). JSON Schema validation is planned for v1.1 once the `schemas/` directory is populated.

### Linking to the spec

Stable citation URL:
```
https://github.com/bigappstudiollc/bippsi/blob/main/ai-standard/SPEC.md
```

Or by tag for a frozen version:
```
https://github.com/bigappstudiollc/bippsi/blob/v1.0.0/ai-standard/SPEC.md
```

## Live reference site

Bippsi.com itself publishes a conforming manifest:

- Manifest: <https://bippsi.com/bippsi-unified.md>
- Human-friendly explainer: <https://bippsi.com/bippsi-standard>
- MCP endpoint declared by the manifest: <https://bippsi.com/api/v1/mcp>

## License

- **SPEC.md, README files, CHANGELOG, examples, schemas** — [CC BY 4.0](../LICENSE-SPEC)
- **Anything in `reference/`** — [MIT](../LICENSE-CODE)

## Contributing

See [../CONTRIBUTING.md](../CONTRIBUTING.md). Quick summary:
- Typos and clarifications → PR directly
- Spec changes → issue first using the "Spec proposal" template
- New language ports → issue first to coordinate with existing `reference/` layout
