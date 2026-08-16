# Hermes Task Actions (B104)

## Flow

1. User opens **Edit Task** → **AI Actions**.
2. Frontend `hermesService.triggerTaskAction(taskId, action)` → `POST /api/agents/hermes/task-actions`.
3. Backend `HermesTaskActionService` loads task + company, writes `HermesGatewayLog`, then:
   - Prefer an active Agent with `executionFramework = HERMES` (or SEO/content domain), via `AgentRunnerService.runViaHermes` → `POST ${HERMES_GATEWAY_URL}/v1/agents/run`.
   - Fallback: direct axios POST to the same Hermes gateway with the task-action payload.
4. UI polls `GET /api/agents/hermes/task-actions/:logId` until status is terminal.

## Actions

| Action key | UI label | Intent |
|---|---|---|
| `generate_seo_audit` | Generate SEO Audit | Technical/on-page/content audit outline |
| `draft_content_structure` | Draft Content Structure | H1/H2 outline + keywords + CTA |

## Env

- `HERMES_GATEWAY_URL` — required for gateway calls
- `HERMES_GATEWAY_TOKEN` — optional Bearer token

## Schema

See `hermes-task-action-schema.json` in this folder.
