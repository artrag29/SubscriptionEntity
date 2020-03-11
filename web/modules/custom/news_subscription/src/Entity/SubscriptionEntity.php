<?php

namespace Drupal\news_subscription\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Subscription entity entity.
 *
 * @ingroup news_subscription
 *
 * @ContentEntityType(
 *   id = "subscription_entity",
 *   label = @Translation("Subscription entity"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\news_subscription\SubscriptionEntityListBuilder",
 *     "views_data" = "Drupal\news_subscription\Entity\SubscriptionEntityViewsData",
 *     "translation" = "Drupal\news_subscription\SubscriptionEntityTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\news_subscription\Form\SubscriptionEntityForm",
 *       "add" = "Drupal\news_subscription\Form\SubscriptionEntityForm",
 *       "edit" = "Drupal\news_subscription\Form\SubscriptionEntityForm",
 *       "delete" = "Drupal\news_subscription\Form\SubscriptionEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\news_subscription\SubscriptionEntityHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\news_subscription\SubscriptionEntityAccessControlHandler",
 *   },
 *   base_table = "subscription_entity",
 *   data_table = "subscription_entity_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer subscription entity entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "category" = "category",
 *     "mail" = "mail",
 *   },
 *   links = {
 *     "canonical" = "/subscribe/subscription_entity/{subscription_entity}",
 *     "add-form" = "/subscribe/subscription_entity/add",
 *     "edit-form" = "/subscribe/subscription_entity/{subscription_entity}/edit",
 *     "delete-form" = "/subscribe/subscription_entity/{subscription_entity}/delete",
 *     "collection" = "/subscribe/subscription_entity",
 *   },
 *   field_ui_base_route = "subscription_entity.settings"
 * )
 */
class SubscriptionEntity extends ContentEntityBase implements SubscriptionEntityInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCategory() {
    return $this->get('category')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCategory($category) {
    $this->set('category', $category);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getMail() {
    return $this->get('mail')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setMail($mail) {
    $this->set('mail', $mail);
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Subscription entity entity.'))
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default');


    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Subscriber.'))
      ->setSettings([
        'max_length' => 80,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -2,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -2,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $news_vocab = \Drupal::entityManager()->getStorage('taxonomy_term')->loadTree('news_categories');

    foreach ($news_vocab as $term) {
      $term_data[$term->tid] = $term->name;
    }

    $fields['category'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Select news category'))
      ->setRequired(TRUE)
      ->setSetting('allowed_values', $term_data ?? '')
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => 10,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['mail'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Email'))
      ->setSettings([
        'max_length' => 80,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -1,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -1,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);


    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
