# Bippsi AI Standard — Changelog

All notable changes to this specification are documented here. Semver applies: **major** for breaking changes, **minor** for additive changes, **patch** for clarifications.

---

## [1.0] — 2026-04-14

Initial public release.

### Added

- Two-layer specification: legacy individual-file discovery + unified `/bippsi-unified.md` manifest
- 9 required sections defined with exact ATX headings:
  1. Identity
  2. Agent access policy
  3. MCP (Model Context Protocol) server
  4. REST API
  5. Certification status
  6. Content licensing for AI training
  7. Page index
  8. Fallback — individual discovery files
  9. Spec metadata
- 3 optional sections: Agent payments (10), Capabilities (11), Changelog (12)
- Agent discovery algorithm — 5-step fallback from unified manifest to HTML scraping
- Proposed `/.well-known/mcp.json` as the MCP remote-server discovery URL (the MCP spec itself doesn't standardize one)
- Integration notes for the A.I. Certified scanner: +1 check in `agent_native` for sites publishing a valid manifest, +1 in `ai_discovery` for declaring `spec_version >= 1.0`
- Reference implementations in PHP, Node, and Python (all dependency-free, standard library only)

### Notes

- First live deployment: bippsi.com/bippsi-unified.md (2026-04-14, reference implementation)
- Spec text licensed CC BY 4.0; reference code licensed MIT
