# bippsi

Public home for Bippsi's open specifications, reference implementations, and shared artifacts.

Bippsi is building the agent-native layer of the web. This repo holds the parts of that work that belong in the open — so anyone can cite them, implement them, or propose changes.

Commercial code (the bippsi.com application, customer-specific plugins, internal tooling) lives elsewhere and stays private.

## What's in here

### [`ai-standard/`](./ai-standard/) — Bippsi AI Standard

A specification for how websites publish agent-discoverable capabilities. Two layers:

- **Legacy discovery** — the existing individual files (`robots.txt`, `llms.txt`, `AGENTS.md`, `openapi.json`, `/.well-known/mcp.json`, etc.)
- **Unified manifest** — a single `/bippsi-unified.md` file at the site root that replaces the ten-file discovery hunt

Includes portable reference implementations in PHP, Node, and Python.

**Current version:** v1.0 (2026-04-14)
**Spec:** [`ai-standard/SPEC.md`](./ai-standard/SPEC.md)
**Live reference site:** <https://bippsi.com/bippsi-unified.md>
**Human-friendly explainer:** <https://bippsi.com/bippsi-standard>

---

## Licensing

Two licenses, applied by file type:

- **Specification text** — [Creative Commons Attribution 4.0](./LICENSE-SPEC). Republish, translate, embed, or build on the spec freely, with attribution to Bippsi.
- **Reference code** (anything in `reference/` directories) — [MIT](./LICENSE-CODE). Use, fork, modify without restriction.

This matches how CloudEvents, Dapr, and similar open-standard projects split their licensing.

## Contributing

See [CONTRIBUTING.md](./CONTRIBUTING.md). Short version:
- Spec changes → open a GitHub issue using the "Spec proposal" template
- Reference implementation bugs or new language ports → PR welcome
- Large changes → discuss in an issue first

## About Bippsi

- Website: <https://bippsi.com>
- Legal entity: Big App Studio LLC
- Contact: admin@bippsi.com

Products built on top of this work:
- [**Agent Initiative**](https://bippsi.com/agent-initiative) — AI compliance certification scanner + platform
- [**Strategy Ninja**](https://bippsi.com/apps/strategy-ninja), [**License Ninja**](https://bippsi.com/apps/license-ninja), [**Social Ninja**](https://bippsi.com/apps/social-ninja) — SaaS apps on the Bippsi hub
