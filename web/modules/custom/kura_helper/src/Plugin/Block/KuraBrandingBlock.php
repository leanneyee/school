<?php

declare(strict_types=1);

namespace Drupal\kura_helper\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ThemeExtensionList;
use Drupal\Core\Extension\ThemeSettingsProvider;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\system\Plugin\Block\SystemBrandingBlock;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Extends the core site branding block with a dark/light logo variant option.
 */
#[Block(
  id: 'kura_branding_block',
  admin_label: new TranslatableMarkup('Site branding'),
)]
class KuraBrandingBlock extends SystemBrandingBlock {

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    ConfigFactoryInterface $configFactory,
    ThemeSettingsProvider $themeSettingsProvider,
    private readonly ThemeExtensionList $themeList,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $configFactory, $themeSettingsProvider);
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory'),
      $container->get(ThemeSettingsProvider::class),
      $container->get('extension.list.theme'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return parent::defaultConfiguration() + ['logo_variant' => 'dark'];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state): array {
    $form = parent::blockForm($form, $form_state);

    $form['block_branding']['logo_variant'] = [
      '#type' => 'select',
      '#title' => $this->t('Logo variant'),
      '#description' => $this->t('Choose the variant that suits the background colour of this block placement.'),
      '#options' => [
        'dark' => $this->t('Dark (for light backgrounds)'),
        'light' => $this->t('Light (for dark backgrounds)'),
      ],
      '#default_value' => $this->configuration['logo_variant'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state): void {
    parent::blockSubmit($form, $form_state);
    $this->configuration['logo_variant'] = $form_state->getValue(['block_branding', 'logo_variant']);
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $build = parent::build();

    if ($this->configuration['use_site_logo']) {
      $logo_file = $this->configuration['logo_variant'] === 'light' ? 'logo-light.svg' : 'logo-dark.svg';
      $build['site_logo']['#uri'] = \base_path() . $this->themeList->getPath('kura') . '/' . $logo_file;
    }

    return $build;
  }

}
