# Cortijo production deployment

Cortijo production deploys automatically when `main` is updated.

## Production facts

- Server: `100.120.149.23` over Tailscale
- Webroot: `/var/www/html/erp`
- Server staging clone: `/opt/cortijo/repo`
- Repo: `https://github.com/rortizs/cortijo`
- Runtime: PHP 5.6 only

## Required GitHub secrets

| Secret | Purpose |
| --- | --- |
| `TS_AUTHKEY` | Ephemeral/reusable Tailscale auth key for the GitHub runner. It must allow the runner to reach `100.120.149.23`. |
| `CORTIJO_DEPLOY_SSH_KEY` | Private SSH key allowed to SSH into the production server. |
| `CORTIJO_DEPLOY_HOST` | Optional override; defaults to `100.120.149.23`. |
| `CORTIJO_DEPLOY_USER` | Optional override; defaults to `root`. |
| `CORTIJO_SSH_KNOWN_HOSTS` | Optional pinned known_hosts content. If absent, the workflow uses `ssh-keyscan`. |

## Server setup

Keep PHP 5.6 packages held before installing deployment tooling:

```bash
apt-mark hold php5.6 php5.6-cli php5.6-common php5.6-curl php5.6-json php5.6-mbstring php5.6-mysql php5.6-opcache php5.6-readline php5.6-xml libapache2-mod-php5.6
```

Install required runtime tools without upgrading PHP:

```bash
apt-get update
apt-get install -y --no-install-recommends git rsync tar openssh-server
```

Do not run `apt upgrade`, `apt dist-upgrade`, or `apt autoremove` on this legacy ERP server.

Create an SSH deploy key on the Mac or locally:

```bash
ssh-keygen -t ed25519 -f ~/.ssh/cortijo_github_actions -C cortijo-github-actions
```

Add the public key to production:

```bash
ssh root@100.120.149.23 'mkdir -p ~/.ssh && chmod 700 ~/.ssh'
cat ~/.ssh/cortijo_github_actions.pub | ssh root@100.120.149.23 'cat >> ~/.ssh/authorized_keys && chmod 600 ~/.ssh/authorized_keys'
```

Store the private key as `CORTIJO_DEPLOY_SSH_KEY` in GitHub repository secrets.

## Deployment behavior

The workflow SSHes into production and runs `scripts/deploy/cortijo-production.sh` on the server. The script:

1. verifies PHP CLI and Apache use PHP 5.6;
2. clones/fetches the repo under `/opt/cortijo/repo`;
3. checks out the deployed commit;
4. runs PHP 5.6 syntax validation on deployable source files before changing webroot;
5. backs up `/var/www/html/erp` to `/root/cortijo-backups`;
6. rsyncs `repo/erp/` into `/var/www/html/erp/` without deleting production-only files;
7. uses checksum comparison so same-content files are not rewritten only because mtimes differ;
8. excludes `models/config.php`, `models/configuraciones.php`, known repo-only legacy admin/ticket files, logs, temp directories, IDE files, and upload/media folders:
   - `assets/docClientes/`
   - `assets/images/clientes/`
   - `assets/images/empleados/`
   - `assets/images/productos/`
9. lints deployed PHP files;
10. re-checks PHP 5.6.

Manual reruns are available through `workflow_dispatch` in GitHub Actions.
