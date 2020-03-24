# Magento 2 In a hurry 

Blog post coming shortly on https://store.fooman.co.nz/blog

Use from template

Add secrets
REPO_MAGENTO_PASSWORD
REPO_MAGENTO_USERNAME
SSH_PRIVATE_KEY
SSH_KNOWN_HOSTS
ssh-keyscan rsa -t <server IP>

Configure m2-deploy-settings.txt
commit and push

## How it works
Commits to Master branch will trigger a Github action running the checkout integration tests. If successful deployed to your staging environment.

Tagged commits will trigger a longer execution of integration tests, if successful deploy to production environment is executed.
