# Tome Demonstration

This is a proof of concept to show how a Drupal 8 site could be built and hosted using [Tome](https://drupal.org/project/tome), GitHub Actions and GitHub Pages. 

This is not a perfect setup for doing this by any means. However, I wanted to show how to do this using GitHub Actions and GitHub pages as they are inexpensive and can get the job done with a little but of work. You could easily apply these same strategies to a sandbox site on Pantheon and host your Drupal site for free.


## How it Works

1. A cheap VPS is hosting the Drupal site.
1. The Drupal site calls out to GitHub and kicks off a deployment.
1. The deploy workflow in the `.github/workflows/` directory listens for deployments and starts the build process.
1. The workflow checks out the repo and builds the site
1. THe workflow pulls the site files and database and sets up a clone of the live site
1. The workflow runs the Tome generator 
1. The workflow publishes the generated static site to the `gh-pages` branch
1. Finally, the workflow marks the deployment a sucess (or failure if things went wrong).


## Interesting Areas of the Code

1. [Drupal + GitHub Integration](docroot/modules/custom/tome_deploy_gh_actions/src/Plugin/FrontendEnvironment/GitHubActionsFrontendEnvironment.php)
1. [Deploy Workflow](.github/workflows/deploy-github-pages.yml)
1. [Tome Deploy](private/scripts/deploy-tome.sh)

