---
name: Spec proposal
about: Propose a change to the Bippsi AI Standard (add/modify/remove a section, field, or requirement)
title: "[spec] "
labels: spec-proposal
assignees: ''
---

## Summary

One-sentence description of what you're proposing.

## What changes

- **Affected spec section(s):** (e.g. "section 3 — MCP server" or "agent discovery algorithm")
- **Kind of change:** addition / modification / removal / clarification
- **Breaks backward compatibility?** yes / no

## Why

What problem does this solve? What are agents, sites, or implementers currently missing, or what's ambiguous? If this fixes a real-world interop issue, link to the incident.

## Proposed text

Paste the proposed spec text, or link to a draft. For larger changes, a PR against SPEC.md on a branch is easier to review than text in this issue.

## Example

Show the before/after in a conforming manifest snippet. For example:

**Before:**
```markdown
## 3. MCP (Model Context Protocol) server
```json
{ ... }
```
```

**After:**
```markdown
## 3. MCP (Model Context Protocol) server
```json
{ ... new_field_here: "..." ... }
```
```

## Migration path (for breaking changes only)

If this is a major-version change, describe how existing manifests migrate. Include a timeline for deprecation if needed.

## References

- Related issues / PRs in this repo
- External standards this aligns with (MCP spec, schema.org, etc.)
- Prior art (other projects that handle this the same way)
