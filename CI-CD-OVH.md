# GitHub Actions deployment to OVH

The workflow in `.github/workflows/deploy-ovh.yml` deploys pushes to `main` and can also be started manually from GitHub Actions.

## Before the first push

1. Create an empty private GitHub repository.
2. Confirm `.env.prod` is not tracked. It is ignored locally; keep the real production `.env` only on OVH.
3. Create an SSH key pair dedicated to GitHub Actions. Keep the private key locally and add the public key to the OVH account's `~/.ssh/authorized_keys`.
4. Add these repository secrets in GitHub under **Settings > Secrets and variables > Actions**:

   - `OVH_HOST`: `ssh.cluster100.hosting.ovh.net`
   - `OVH_USERNAME`: your OVH SSH username
   - `OVH_SSH_PORT`: `22`
   - `OVH_SSH_PRIVATE_KEY`: the complete private key, including its BEGIN and END lines

5. Push the repository's `main` branch.

## Deployment safety

The workflow preserves the production `.env` and `storage/app/public` media. It copies the built public assets to the OVH web root and validates Composer and Laravel bootstrap. It does not run migrations, seeders, password resets, or database cleanup.

The first deployment should be run manually and checked in the Actions log before enabling normal pushes to `main`.
