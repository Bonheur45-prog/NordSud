#!/bin/bash
set -e

# ── CONFIGURE THESE ──────────────────────────────────────────────
OVH_USER="your-ovh-ssh-user"
OVH_HOST="ssh.cluster0XX.hosting.ovh.net"
OVH_PATH="/home/${OVH_USER}/www"          # or wherever your domain's docroot's PARENT is
STAGING_DOMAIN="new.nsi-monetique.com"    # staging subdomain — NOT the live domain
# ─────────────────────────────────────────────────────────────────

echo "==> Building frontend assets"
npm run build

echo "==> Syncing files to OVH (excludes local-only files)"
rsync -avz --delete \
  --exclude='.git' \
  --exclude='.env' \
  --exclude='node_modules' \
  --exclude='storage/logs/*' \
  --exclude='storage/framework/cache/*' \
  --exclude='storage/framework/sessions/*' \
  --exclude='storage/framework/views/*' \
  ./ "${OVH_USER}@${OVH_HOST}:${OVH_PATH}/"

echo "==> Running remote post-deploy steps"
ssh "${OVH_USER}@${OVH_HOST}" "cd ${OVH_PATH} && \
  composer install --no-dev --optimize-autoloader && \
  php artisan migrate --force && \
  php artisan config:cache && \
  php artisan route:cache && \
  php artisan view:cache && \
  php artisan storage:link || true"

echo "==> Done. Check https://${STAGING_DOMAIN}"
