# Magento 2 In a hurry 

Blog post coming shortly on https://store.fooman.co.nz/blog

## How to use this repository

Click "Use this template" to take a copy of this starter repository. Make it private. 

Then head to settings for the new repository and add the following Secrets
```
REPO_MAGENTO_PASSWORD
REPO_MAGENTO_USERNAME
```
Enter the private key and public key after following http://devdocs.magento.com/guides/v2.0/install-gde/prereq/connect-auth.html

```
SSH_PRIVATE_KEY
```
Private key which has ssh access to the server you want to be deploying to. The same key is used for accessing the staging and production environment.


```
SSH_KNOWN_HOSTS

```
Provide details on the staging and production servers. Output of the below

`cat ~/.ssh/known_hosts` or `ssh-keyscan -t <server IP>`


Edit `m2-deploy-settings.txt` with your values for both staging and production environments.
commit and push

## How it works
Commits to Master branch will trigger a Github action running the checkout integration tests. If successful deployed to your staging environment.

Tagged commits will trigger a longer execution of integration tests, if successful deploy to production environment is executed.

It is anticipated that the first deploys will not succeed. After running the first deploy please provide a valid `app/etc/env.php` on the server directly. Alternatively run `bin/magento setup:install` to create one.

## Links
https://help.github.com/en/github/authenticating-to-github/generating-a-new-ssh-key-and-adding-it-to-the-ssh-agent
https://desktop.github.com/