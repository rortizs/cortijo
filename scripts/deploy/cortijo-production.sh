#!/usr/bin/env bash
set -euo pipefail

REPO_URL="${REPO_URL:-https://github.com/rortizs/cortijo.git}"
REPO_DIR="${REPO_DIR:-/opt/cortijo/repo}"
WEBROOT="${WEBROOT:-/var/www/html/erp}"
BACKUP_DIR="${BACKUP_DIR:-/root/cortijo-backups}"
DEPLOY_REF="${DEPLOY_REF:-origin/main}"
DEPLOY_COMMIT="${DEPLOY_COMMIT:-}"

log() {
	printf '[cortijo-deploy] %s\n' "$*"
}

fail() {
	printf '[cortijo-deploy] ERROR: %s\n' "$*" >&2
	exit 1
}

need_cmd() {
	command -v "$1" >/dev/null 2>&1 || fail "Required command not found: $1"
}

for cmd in git rsync tar php apache2ctl; do
	need_cmd "$cmd"
done

verify_php_runtime() {
	php -v | head -n 1 | grep -Eq '^PHP 5\.6\.' ||
		fail "PHP CLI must be PHP 5.6 before deployment"

	apache_output="$(apache2ctl -M 2>&1 || true)"
	if ! printf '%s\n' "$apache_output" | grep -q 'php5_module' &&
		[ ! -e /etc/apache2/mods-enabled/php5.6.load ]; then
		printf '%s\n' "$apache_output" >&2
		fail "Apache must be using PHP 5.6 before deployment"
	fi
}

log "Verifying PHP 5.6 runtime"
verify_php_runtime

mkdir -p "$(dirname "$REPO_DIR")" "$BACKUP_DIR"

if [ ! -d "$REPO_DIR/.git" ]; then
	if [ -e "$REPO_DIR" ]; then
		fail "$REPO_DIR exists but is not a Git repository"
	fi
	log "Cloning repository into $REPO_DIR"
	git clone "$REPO_URL" "$REPO_DIR"
else
	log "Fetching repository updates"
	git -C "$REPO_DIR" remote set-url origin "$REPO_URL"
	git -C "$REPO_DIR" fetch origin --prune
fi

if [ -n "$DEPLOY_COMMIT" ]; then
	target="$DEPLOY_COMMIT"
else
	target="$DEPLOY_REF"
fi

log "Checking out $target"
git -C "$REPO_DIR" checkout -f "$target"
git -C "$REPO_DIR" reset --hard "$target"

[ -d "$REPO_DIR/erp" ] || fail "Repository clone does not contain erp/"

if [ -d "$WEBROOT" ]; then
	timestamp="$(date +%Y%m%d-%H%M%S)"
	backup_file="$BACKUP_DIR/erp-$timestamp.tar.gz"
	log "Creating backup: $backup_file"
	tar -czf "$backup_file" -C "$(dirname "$WEBROOT")" "$(basename "$WEBROOT")"
else
	log "Webroot $WEBROOT does not exist; creating it"
	mkdir -p "$WEBROOT"
fi

log "Deploying repo/erp/ to $WEBROOT without deleting production-only files"
rsync -rcl \
	--exclude 'models/config.php' \
	--exclude 'tmp/' \
	--exclude 'logs/' \
	--exclude '*.log' \
	--exclude '.project' \
	--exclude '.settings/' \
	--exclude '.metadata/' \
	--exclude '.classpath' \
	--exclude '.DS_Store' \
	--exclude 'assets/docClientes/' \
	--exclude 'assets/images/clientes/' \
	--exclude 'assets/images/empleados/' \
	--exclude 'assets/images/productos/' \
	"$REPO_DIR/erp/" "$WEBROOT/"

lint_list="$(mktemp)"
lint_output="$(mktemp)"
trap 'rm -f "$lint_list" "$lint_output"' EXIT

while IFS= read -r -d '' repo_file; do
	rel="${repo_file#erp/}"
	case "$rel" in
	models/config.php) continue ;;
	*.php)
		deployed_file="$WEBROOT/$rel"
		[ -f "$deployed_file" ] && printf '%s\0' "$deployed_file" >>"$lint_list"
		;;
	esac
done < <(git -C "$REPO_DIR" ls-files -z -- erp)

if [ -s "$lint_list" ]; then
	lint_count="$(tr -cd '\0' <"$lint_list" | wc -c | tr -d ' ')"
	log "Running php -l on $lint_count deployed PHP files"
	if ! xargs -0 -n 1 php -l <"$lint_list" >"$lint_output"; then
		cat "$lint_output" >&2
		fail "PHP syntax validation failed"
	fi
else
	fail "No deployed PHP files found to lint"
fi

log "Re-checking PHP 5.6 runtime after deploy"
verify_php_runtime

deployed_revision="$(git -C "$REPO_DIR" rev-parse --short HEAD)"
log "Deployment completed successfully"
log "Revision: $deployed_revision"
log "Webroot: $WEBROOT"
log "Backup dir: $BACKUP_DIR"
