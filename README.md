# Magento 2 In a hurry 
Introductory Blog post including video walkthrough for example installation is [here](https://store.fooman.co.nz/blog/magento-2-in-a-hurry.html).

## How to use this repository

### Step 1 - Use this template
Click "Use this template" to take a copy of this starter repository. Make it private. 

### Step 2 - Add Secrets
Then head to settings for the new repository and add the following 4 Secrets

After following http://devdocs.magento.com/guides/v2.0/install-gde/prereq/connect-auth.html

1. `REPO_MAGENTO_USERNAME`
This the public key from the Magento access keys

2. `REPO_MAGENTO_PASSWORD`
This the private key from the Magento access keys

3. `SSH_PRIVATE_KEY`
Private key which has ssh access to the server you want to be deploying to. The same key is used for accessing the staging and production environment.

4. `SSH_KNOWN_HOSTS`
Provide details on the staging and production servers. Output of the below

`cat ~/.ssh/known_hosts`  
or `ssh-keyscan <server IP>` 

### Step 3 - Edit Deploy Settings
Edit `m2-deploy-settings.txt` with your values for both staging and production environments.

### Step 4 - upload changes
```
git commit 
git push
```

## How it works
Commits to Master branch will trigger a Github action running the checkout integration tests. If successful deployed to your staging environment.

Tagged commits will trigger a longer execution of integration tests, if successful deploy to production environment is executed. There is a convenience script included `./push-to-live.sh`

It is anticipated that the first deploys will not succeed. After running the first deploy please provide a valid `app/etc/env.php` on the server directly. Alternatively run `bin/magento setup:install` to create one.

## Includes
Utilises Magento 2 Deployer Plus

Includes common tools
* Composer.phar
* ExtDN_Installer.phar
* Magerun2.phar

Has a pre-set up template under app/design/frontend/Magento/luma-extended to build on top of the Luma theme.

## Links
https://help.github.com/en/github/authenticating-to-github/generating-a-new-ssh-key-and-adding-it-to-the-ssh-agent
https://desktop.github.com/
