# Tome Demonstration

This is a proof of concept to show how a Drupal 8 site could be built and hosted using [Tome](https://drupal.org/project/tome), GitHub Actions and GitHub Pages. 

This is not a perfect setup for doing this by any means. However, I wanted to show how to do this using GitHub Actions and GitHub pages as they are inexpensive and can get the job done with a little but of work. You could easily apply these same strategies to a sandbox site on Pantheon and host your Drupal site for free.

**Helpful Links:**
* [See the Demo](https://tome.curtisogle.com)
* [Actions Logs](https://github.com/daceej/d8-tome-gh-actions/actions)

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


## (Possible) Future Enhancements

1. This currently does not do a good job of tracking the deploy once kicked off on the Drupal site (it doesn't do anything at all, actually).
1. The (Build Hooks)[https://drupal.org/project/build_hooks] module reports changes after the deployment. I'm assuming I'm doing something wrong, but hey -- I built this in a day. I'm also not convinced that the Build Hooks module is here to stay. The project has not had a release in quite a while, so this may be reworked to stand on its own eventually.
1. Hopefully GitHub Actions and other GitHub apis are extended in the future. Right now the feel somewhat limiting.