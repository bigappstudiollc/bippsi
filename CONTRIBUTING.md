# Contributing to the Bippsi AI Standard

Thanks for wanting to help. This repository holds the Bippsi AI Standard — a generic specification for agent-discoverable site manifests — and its reference implementations.

## What kind of change are you making?

### Typo, broken link, or clarification
Open a PR. No issue needed.

### Bug in a reference implementation
Open an issue with a minimal repro, then PR the fix. Include before/after output so reviewers can see what the bug did.

### New language port (Go, Ruby, Rust reference implementation)
Open an issue first so we can coordinate naming and file layout with the existing `reference/php/`, `reference/node/`, `reference/python/` structure. Then PR the port.

### Change to the specification itself
Spec changes follow a proposal process:

1. **Open an issue** using the "Spec proposal" template. Include:
   - What you want to change or add
   - Why — what problem does this solve?
   - What existing spec text is affected
   - Backward compatibility — does this break older manifests?
   - An example snippet, before and after
2. **Discuss** — maintainers and community weigh in. Goal is rough consensus before code.
3. **PR** with the spec change, a CHANGELOG entry, and a migration note for breaking changes.

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

- **PHP** — PSR-12. PHP 8.1+. No external dependencies.
- **JavaScript** — Node 20+. No external dependencies (the reference impl should run with zero installs).
- **Python** — 3.10+. Standard library only.

Reference implementations should be **self-contained**. A new adopter should be able to copy the single file into their project and run it.

## License on contributions

By submitting a PR you agree that:
- Spec-text contributions are licensed under CC BY 4.0 (same as LICENSE-SPEC)
- Code contributions in `reference/` are licensed under MIT (same as LICENSE-CODE)

No CLA. The licenses handle it.

## Not-welcome contributions

- Adding Bippsi branding to the spec. The spec is written to be implementation-agnostic.
- Security reports. If you've found a security issue in the reference code, use the private contact form at <https://bippsi.com/contact>.
- Drive-by dependency additions. Keep reference implementations dependency-free.

## Questions

Reach out via <https://bippsi.com/contact>.
