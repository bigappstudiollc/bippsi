# Contributing to Bippsi open specs

Thanks for wanting to help. This repo holds Bippsi's public specifications and reference implementations — contributions are welcome across all of them.

## What kind of change are you making?

### Typo, broken link, or clarification
Just open a PR. No issue needed. Describe what you fixed in the PR body.

### Bug in a reference implementation
Open an issue with a minimal repro, then PR the fix if you have one. Include before/after output so reviewers can see what the bug did.

### New language port (e.g., Go, Ruby, Rust reference implementation)
Open an issue first so we can coordinate naming and file layout with the existing `reference/php/`, `reference/node/`, `reference/python/` structure. Then PR the port.

### Change to the specification itself
This is the big one. Spec changes follow a proposal process:

1. **Open an issue** using the "Spec proposal" template. Include:
   - What you want to change or add
   - Why — what problem does this solve?
   - What existing spec text is affected (link to specific sections)
   - Backward compatibility — does this break older manifests?
   - An example of the proposed manifest snippet, before and after

2. **Discuss** — the maintainers and community will weigh in. Goal is rough consensus before code, not unanimous agreement.

3. **PR** with the spec change, a CHANGELOG entry, and if it's a breaking change, a migration note.

### Examples I want to see in PRs
- New example manifests for specific platforms (Shopify, Ghost, Hugo, etc.) — add under `ai-standard/examples/`
- JSON schema improvements — `ai-standard/schemas/`
- Better error messages in reference implementations
- Test cases for reference implementations

## Versioning

The spec uses semver:
- **Major** — breaking changes to required sections or JSON schemas
- **Minor** — additive changes (new optional sections, new optional fields)
- **Patch** — typo fixes, clarifications that don't change meaning

Every merge to `main` that changes `SPEC.md` must update `CHANGELOG.md`.

## Code style

- **PHP** — PSR-12. PHP 8.1+ features OK. No Bippsi-specific dependencies.
- **JavaScript** — Node 20+. Prefer no external dependencies (the reference impl should be `node bippsi-unified.js config.json` with zero installs).
- **Python** — 3.10+. Standard library only.

Reference implementations should be **self-contained**. A new adopter should be able to copy the single file into their project and run it. No `npm install`, no `composer require`, no `pip install` — just standard library.

## License on contributions

By submitting a PR you agree that:
- Spec-text contributions are licensed under CC BY 4.0 (same as LICENSE-SPEC)
- Code contributions in `reference/` are licensed under MIT (same as LICENSE-CODE)

No CLA. The licenses themselves handle it.

## Not-welcome contributions

- Adding Bippsi branding/promotion to the spec. The spec is owned by Bippsi but written to be implementation-agnostic — "CMS platform X" not "our platform"
- Security reports. If you've found a security issue in the reference code or a privacy concern in the spec, use the private contact form at <https://bippsi.com/contact> instead of opening a public issue
- "Drive-by" dependency additions to reference implementations. Keep them dependency-free

## Questions

Open a GitHub Discussion (once enabled) or reach out via <https://bippsi.com/contact>.
