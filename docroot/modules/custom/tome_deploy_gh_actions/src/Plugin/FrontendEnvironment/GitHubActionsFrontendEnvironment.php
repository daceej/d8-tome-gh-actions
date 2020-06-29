<?php

namespace Drupal\tome_deploy_gh_actions\Plugin\FrontendEnvironment;

use Drupal\build_hooks\Plugin\FrontendEnvironmentBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerTrait;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\build_hooks\BuildHookDetails;

/**
 * Provides a GitHub Actions frontend environment type.
 *
 * @FrontendEnvironment(
 *  id = "github_actions",
 *  label = "GitHub Actions",
 *  description = "An environment on built by GitHub Actions"
 * )
 */
class GitHubActionsFrontendEnvironment extends FrontendEnvironmentBase implements ContainerFactoryPluginInterface {

  use MessengerTrait;

  /**
   * Construct.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param string $plugin_definition
   *   The plugin implementation definition.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition
    );
  }

  /**
   * {@inheritdoc}
   */
  public function frontEndEnvironmentForm($form, FormStateInterface $form_state) {
    $form['gh_deploy_url'] = [
      '#type' => 'url',
      '#title' => $this->t('GitHub Deploy Hook Url'),
      '#maxlength' => 255,
      '#default_value' => isset($this->configuration['gh_deploy_url']) ? $this->configuration['gh_deploy_url'] : '',
      '#description' => $this->t('The build hook url. (Example: https://api.github.com/repos/your-organization/your-repo/deployments)'),
      '#required' => TRUE,
    ];

    $form['branch'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Git Branch'),
      '#maxlength' => 255,
      '#default_value' => isset($this->configuration['branch']) ? $this->configuration['branch'] : '',
      '#description' => $this->t('The git branch that the build hook refers to. You can use a branch, commit hash, or a tag here.'),
      '#required' => TRUE,
    ];

    $form['gh_token'] = [
      '#type' => 'password',
      '#title' => $this->t('GitHub Personal Access Token'),
      '#maxlength' => 255,
      '#description' => $this->t('Your GitHub personal access token.<br/>Note: It is strongly advised this value be <a href="https://www.drupal.org/project/config_ignore" target="_blank">ignored</a> and added via settings.php similar to how it is described <a href="https://www.drupal.org/docs/8/modules/social-migration/how-to-avoid-storing-api-keys-in-the-codebase-acquia" target="_blank">here</a>.<br/> Example: $config[\'build_hooks.frontend_environment.MACHINE_NAME\'][\'settings\'][\'gh_token\'] = \'YOUR_PERSONAL_ACCESS_TOKEN\';'),
    ];

    if (empty($this->configuration['gh_token'])) {
      $form['gh_token']['#required'] = TRUE;
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function frontEndEnvironmentSubmit($form, FormStateInterface $form_state) {
    $this->configuration['gh_deploy_url'] = $form_state->getValue('gh_deploy_url');
    $this->configuration['branch'] = $form_state->getValue('branch');

    // Only update the token if it was entered and it changed.
    if (
        !empty($form_state->getValue('gh_token'))
        && $this->configuration['gh_token'] !== $form_state->getValue('gh_token') 
      ) {
      $this->configuration['gh_token'] = $form_state->getValue('gh_token');
    }

    // This is technically a subform, but we are interested in some of the
    // parent form's data in other contexts in the getBuildHookDetails() method
    // where it isn't avaliable. So, as workaround, copying some of the parent
    // details for future use.
    $parent_form_state = $form_state->getCompleteFormState();
    $this->configuration['environment_key'] = $parent_form_state->getValue('id');
    $this->configuration['environment_url'] = $parent_form_state->getValue('url');
  }

  /**
   * {@inheritdoc}
   */
  public function getBuildHookDetails() {
    $buildHookDetails = new BuildHookDetails();
    $buildHookDetails->setUrl($this->configuration['gh_deploy_url']);
    $buildHookDetails->setMethod('POST');
    $buildHookDetails->setBody([
      'json' => [
        'ref' => $this->configuration['branch'],
        'environment' => $this->configuration['environment_key'],
        'required_contexts' => [],
        'payload' => [
          'url' => $this->configuration['environment_url'],
        ],
        'auto_merge' => FALSE,
      ],
      'headers' => [
        'Content-Type' => 'application/json',
        'Accept' => 'application/vnd.github.v3+json',
        'Authorization' => 'token ' . $this->configuration['gh_token'],
      ],
    ]);
    return $buildHookDetails;
  }

  /**
   * {@inheritdoc}
   */
  public function getAdditionalDeployFormElements(FormStateInterface $form_state) {
    $form = [];
    return $form;
  }

}
