<?php

namespace Drupal\news_subscription\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Subscription entity entities.
 *
 * @ingroup news_subscription
 */
interface SubscriptionEntityInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Subscription entity name.
   *
   * @return string
   *   Name of the Subscription entity.
   */
  public function getName();

  /**
   * Sets the Subscription entity name.
   *
   * @param string $name
   *   The Subscription entity name.
   *
   * @return \Drupal\news_subscription\Entity\SubscriptionEntityInterface
   *   The called Subscription entity entity.
   */
  public function setName($name);

  /**
   * Gets the Subscription entity category.
   *
   * @return string
   *   Category of the Subscription entity.
   */
  public function getCategory();

  /**
   * Sets the Subscription entity category.
   *
   * @param string $category
   *   The Subscription entity category.
   *
   * @return \Drupal\news_subscription\Entity\SubscriptionEntityInterface
   *   The called Subscription entity entity.
   */
  public function setCategory($category);

  /**
   * Gets the Subscription entity mail.
   *
   * @return string
   *   Mail of the Subscription entity.
   */
  public function getMail();

  /**
   * Sets the Subscription entity mail.
   *
   * @param string $mail
   *   The Subscription entity mail.
   *
   * @return \Drupal\news_subscription\Entity\SubscriptionEntityInterface
   *   The called Subscription entity entity.
   */
  public function setMail($mail);

  /**
   * Gets the Subscription entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Subscription entity.
   */
  public function getCreatedTime();

  /**
   * Sets the Subscription entity creation timestamp.
   *
   * @param int $timestamp
   *   The Subscription entity creation timestamp.
   *
   * @return \Drupal\news_subscription\Entity\SubscriptionEntityInterface
   *   The called Subscription entity entity.
   */
  public function setCreatedTime($timestamp);

}
