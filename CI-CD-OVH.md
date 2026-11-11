# Deploying to NSI Monétique on OVH

The workflow in `.github/workflows/deploy-ovh.yml` deploys pushes to `main` and can also be started manually from GitHub Actions.

## GitHub Actions secrets

Update the existing repository secrets under **Settings > Secrets and variables > Actions**:

- `OVH_HOST`: `ssh.cluster127.hosting.ovh.net`
- `OVH_USERNAME`: `nsimonetml-Bonheur20`
- `OVH_SSH_PORT`: `22`
- `OVH_SSH_PRIVATE_KEY`: keep the existing deploy private key; its matching public key must be authorized in the NSI account's `~/.ssh/authorized_keys`

The workflow deploys the Laravel application to `/homez.380/nsimonetml/nordsud-app` and copies built public assets to `/homez.380/nsimonetml/www`. The live `www/index.php`, `.env`, storage directory, and public-storage symlink are preserved.

## Production requirements

- The NSI account-level PHP version must remain 8.2 or newer.
- Keep the production `.env` only on OVH. It must use `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://www.nsi-monetique.com`, and `DB_PREFIX=nsi_`.
- Uploaded media is stored in `storage/app/public`; the workflow deliberately excludes it from deployment archives.

The workflow builds with PHP 8.2 and Node.js 20. It does not run migrations, seeders, password resets, or database cleanup. After deployment, it checks required files and requests the live site.

The previous WordPress site is archived outside the web root. The current Finpay site and its deployment files are not changed by this workflow.
