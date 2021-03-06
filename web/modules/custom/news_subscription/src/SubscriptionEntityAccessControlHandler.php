<?php

namespace Drupal\news_subscription;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Subscription entity entity.
 *
 * @see \Drupal\news_subscription\Entity\SubscriptionEntity.
 */
class SubscriptionEntityAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\news_subscription\Entity\SubscriptionEntityInterface $entity */

    switch ($operation) {

      case 'view':

        return AccessResult::allowedIfHasPermission($account, 'view published subscription entity entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit subscription entity entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete subscription entity entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add subscription entity entities');
  }


}
