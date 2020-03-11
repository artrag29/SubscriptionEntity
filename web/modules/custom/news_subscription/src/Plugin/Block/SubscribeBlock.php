<?php

namespace Drupal\news_subscription\Plugin\Block;

use Drupal;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'SubscribeBlock' block.
 *
 * @Block(
 *  id = "subscribe_block",
 *  admin_label = @Translation("Subscribe block"),
 * )
 */
class SubscribeBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $entity = Drupal\news_subscription\Entity\SubscriptionEntity::create();
    $form = \Drupal::service('entity.form_builder')->getForm($entity);

    return $form;
  }

}