# bippsi

Public specifications and reference artifacts from Bippsi.

Bippsi is building the agent-native layer of the web. Most of what we build is proprietary; this repository is the small subset of our work that belongs in the open.

## What's here

### [`ai-standard/`](./ai-standard/) — Bippsi AI Standard

A generic specification for how websites publish agent-discoverable capabilities — a unified manifest at `/bippsi-unified.md` that consolidates what would otherwise be ten separate discovery files (robots.txt, llms.txt, AGENTS.md, openapi.json, `/.well-known/mcp.json`, etc.).

Open for anyone to implement. Reference implementations in PHP, Node, and Python.

**Current version:** v1.0 (2026-04-14)
**Spec:** [`ai-standard/SPEC.md`](./ai-standard/SPEC.md)
**Live reference:** <https://bippsi.com/bippsi-unified.md>

---

## Bippsi's commercial products

Everything else — the agent payment layer, partner integrations, SDKs, training profiles, plugin/connector code, protocol internals — is part of Bippsi's commercial offering and is distributed directly to verified partners, not published publicly.

- **Website:** <https://bippsi.com>
- **For agents:** <https://bippsi.com/for-agents>
- **For site owners:** <https://bippsi.com/agent-initiative>
- **Contact:** <https://bippsi.com/contact>

## Licensing

Specification text in this repository is [Creative Commons Attribution 4.0](./LICENSE-SPEC). Reference code in `reference/` directories is [MIT](./LICENSE-CODE).

## Contributing

Pull requests and issues welcome on the AI Standard spec and its reference implementations. See [CONTRIBUTING.md](./CONTRIBUTING.md).

## About

- Legal entity: Big App Studio LLC
- Support: <https://bippsi.com/contact>
