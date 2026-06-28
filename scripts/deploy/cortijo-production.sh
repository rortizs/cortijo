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

is_excluded_deploy_path() {
	case "$1" in
	models/config.php | \
	models/configuraciones.php | \
	controllers/adminsController.php | \
	models/admins.php | \
	views/reportes/ticket.php | \
	views/reportes/ticket.jrxml | \
	views/reportes/ticket.jasper | \
	tmp/* | \
	logs/* | \
	*.log | \
	.project | \
	.settings/* | \
	.metadata/* | \
	.classpath | \
	.DS_Store | \
	assets/docClientes/* | \
	assets/images/clientes/* | \
	assets/images/empleados/* | \
	assets/images/productos/*)
		return 0
		;;
	*)
		return 1
		;;
	esac
}

build_lint_lists() {
	: >"$source_lint_list"
	: >"$deployed_lint_list"

	while IFS= read -r -d '' repo_file; do
		rel="${repo_file#erp/}"
		is_excluded_deploy_path "$rel" && continue

		case "$rel" in
		*.php)
			printf '%s\0' "$REPO_DIR/$repo_file" >>"$source_lint_list"
			deployed_file="$WEBROOT/$rel"
			[ -f "$deployed_file" ] && printf '%s\0' "$deployed_file" >>"$deployed_lint_list"
			;;
		esac
	done < <(git -C "$REPO_DIR" ls-files -z -- erp)
}

run_php_lint_list() {
	label="$1"
	list_file="$2"
	output_file="$3"

	if [ ! -s "$list_file" ]; then
		fail "No PHP files found to lint for $label"
	fi

	lint_count="$(tr -cd '\0' <"$list_file" | wc -c | tr -d ' ')"
	log "Running php -l on $lint_count $label PHP files"
	if ! xargs -0 -n 1 php -l <"$list_file" >"$output_file" 2>&1; then
		cat "$output_file" >&2
		fail "PHP syntax validation failed for $label"
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

source_lint_list="$(mktemp)"
deployed_lint_list="$(mktemp)"
lint_output="$(mktemp)"
trap 'rm -f "$source_lint_list" "$deployed_lint_list" "$lint_output"' EXIT

build_lint_lists
run_php_lint_list "source" "$source_lint_list" "$lint_output"

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
	--exclude 'models/configuraciones.php' \
	--exclude 'controllers/adminsController.php' \
	--exclude 'models/admins.php' \
	--exclude 'views/reportes/ticket.php' \
	--exclude 'views/reportes/ticket.jrxml' \
	--exclude 'views/reportes/ticket.jasper' \
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

build_lint_lists
run_php_lint_list "deployed" "$deployed_lint_list" "$lint_output"

log "Re-checking PHP 5.6 runtime after deploy"
verify_php_runtime

deployed_revision="$(git -C "$REPO_DIR" rev-parse --short HEAD)"
log "Deployment completed successfully"
log "Revision: $deployed_revision"
log "Webroot: $WEBROOT"
log "Backup dir: $BACKUP_DIR"
