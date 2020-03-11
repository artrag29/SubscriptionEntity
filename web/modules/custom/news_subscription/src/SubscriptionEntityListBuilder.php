<?php

namespace Drupal\news_subscription;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;
use Drupal\taxonomy\Entity\Term;


/**
 * Defines a class to build a listing of Subscription entity entities.
 *
 * @ingroup news_subscription
 */
class SubscriptionEntityListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['name'] = $this->t('Subscriber Name');
    $header['mail'] = $this->t('Subscriber Email');
    $header['category'] = $this->t('Subscribed category');

    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\news_subscription\Entity\SubscriptionEntity $entity */
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.subscription_entity.edit_form',
      ['subscription_entity' => $entity->id()]
    );
    $row['mail'] = $entity->getMail();

    $tid = $entity->getCategory();
    $row['category'] = Term::load($tid)->getName();

    return $row + parent::buildRow($entity);
  }

}
