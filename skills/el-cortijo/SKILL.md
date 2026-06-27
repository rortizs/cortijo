---
name: el-cortijo
description: "Trigger: Cortijo, El Cortijo, cortijo production, on-premise, Tailscale, deploy Cortijo. Operate Ferretería El Cortijo ERP production safely."
license: Apache-2.0
metadata:
  author: gentleman-programming
  version: "1.0"
---

## Activation Contract

Use this skill for Ferretería El Cortijo ERP production work: deploys, hotfix validation, Tailscale access, GitHub Actions rollout, or on-premise server diagnosis.

Do not use it for the shared/global ERP unless the user explicitly confirms that scope.

## Hard Rules

- Production host is on-premise via Tailscale: `100.120.149.23`.
- Active webroot is `/var/www/html/erp`.
- Staging Git clone is `/opt/cortijo/repo`; repo layout is `repo/erp/`.
- GitHub repo is `https://github.com/rortizs/cortijo`.
- PHP 5.6 is mandatory. Verify CLI and Apache before and after deploy.
- Never run `apt upgrade`, `apt dist-upgrade`, or `apt autoremove` during deploy work.
- Always create a backup before changing `/var/www/html/erp`.
- Deploy only after PR review/validation and merge to `main`.
- Preserve production-only `models/config.php` and upload/media directories.

## Decision Gates

| Situation | Action |
| --- | --- |
| Need production deploy | Use GitHub Actions or the deploy script; avoid manual copies. |
| Media/upload changes requested | Treat as production data; require explicit approval. |
| PHP is not 5.6 | Stop; fix runtime plan explicitly before deploying. |
| Webroot differs from `/var/www/html/erp` | Stop and re-validate Apache vhost before writing. |
| Server package changes requested | Ask for explicit approval and rollback plan. |
| Dashboard daily sales show zero | Verify data date first; zero is valid when no `fechaFactura = CURDATE()` rows exist. |

## Execution Steps

1. Confirm target is Cortijo on-premise, not cloud/global ERP.
2. Check repo status locally and ensure changes reached `main` through PR/merge.
3. Deploy through GitHub Actions or run the server script intentionally.
4. Verify backup exists under `/root/cortijo-backups`.
5. Verify `php -v` reports PHP 5.6 and Apache reports `php5_module`/`php5.6.load`.
6. Validate the affected ERP behavior after deploy.

## Output Contract

Return deployed revision, backup path, PHP/Apache verification, validation commands, and residual risk.

## References

- `.github/workflows/deploy-production.yml`
- `scripts/deploy/cortijo-production.sh`
- `docs/production/cortijo-github-actions.md`
