---
name: create-skill
description: "Use when turning a repeated workflow, debugging method, review checklist, or implementation pattern into a reusable SKILL.md. This includes extracting the step-by-step process, identifying decision points and branching logic, clarifying scope, drafting the skill frontmatter and body, validating the trigger wording and file placement, and finalizing with example prompts and related customizations."
license: MIT
metadata:
  author: copilot
---

# Create a Reusable Skill

Use this skill when the user wants a multi-step workflow packaged into a reusable custom skill instead of leaving it as ad hoc instructions.

## Goal

Turn a real process into a durable skill that another agent or user can invoke on demand. The skill should encode the workflow, the decision points, the quality checks, and the final output expected.

## Decide the scope

Before writing the skill, choose the right scope:

- Workspace-shared workflow: place the skill in `.github/skills/<name>/SKILL.md`
- Personal workflow: use the user prompt folder for prompts or instructions, not a workspace skill
- Single focused task: prefer a prompt instead of a skill
- Broad project-wide behavior: prefer instructions or an agent if it applies everywhere

## Workflow to follow

1. Review the conversation or the task flow already in progress.
2. Extract the actual process being followed, including every meaningful step in order.
3. Identify branch points, exceptions, and decision rules.
4. Decide whether the process is reusable, and whether it is a skill, prompt, or instruction.
5. Clarify missing details only when the workflow is ambiguous or underspecified.
6. Draft the skill with valid YAML frontmatter and a clear description that contains the trigger phrases.
7. Include a practical body with sections such as:
   - When to use
   - Goal or outcome
   - Workflow steps
   - Decision logic
   - Completion criteria
   - Example prompts
8. Validate the skill for clarity, scoping, and discoverability.
9. Summarize what it produces and suggest the next related customization.

## Decision rules

- If the workflow has multiple steps and reusable value, create a skill.
- If the task is single-purpose and parameterized, prefer a prompt.
- If it applies to all project work, prefer instructions.
- If the workflow depends on context isolation or tool restrictions, prefer a custom agent.
- If there is no clear workflow yet, ask the user what outcome they want and what scope the skill should cover.

## Good skill design

A strong skill should do all of the following:

- State the exact task or problem it solves.
- Include a description that triggers on recognizable phrases such as "use when..." or "when creating...".
- Separate steps from decision rules so the workflow is easy to follow.
- Explain what success looks like before the task is considered complete.
- Keep the process practical and scoped to the project or user need.
- Avoid vague instructions that could be applied to everything.

## Quality checklist

Before finalizing, verify:

- The skill name matches the folder name.
- The file is created in the correct project location.
- The YAML frontmatter is valid.
- The description is specific and discoverable.
- The workflow is actionable and step-by-step.
- Decision points are explicit.
- Completion checks are included.
- Example prompts are provided if useful.

## Final output

Once the skill is drafted:

- Save it to the chosen path.
- Summarize what the skill produces.
- Recommend example prompts that trigger it.
- Suggest related customizations to create next, such as a prompt, instruction file, or custom agent.

## Example prompts

- "Create a SKILL.md for my debugging workflow."
- "Turn this review checklist into a reusable skill."
- "Package our deployment validation process into a project skill."
- "Make a skill that captures how we build and verify Laravel features."

## Related customizations

After a skill is created, consider:

- a prompt for a single repeated action
- a project instruction for always-on guidance
- a custom agent for context-isolated multi-step work
- a hook for deterministic workflow enforcement
