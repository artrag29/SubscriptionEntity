<?php

namespace Drupal\news_subscription\Form;

use Drupal;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for Subscription entity edit forms.
 *
 * @ingroup news_subscription
 */
class SubscriptionEntityForm extends ContentEntityForm {

  /**
   * The current user account.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $account;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    $instance = parent::create($container);
    $instance->account = $container->get('current_user');
    return $instance;
  }


  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var \Drupal\news_subscription\Entity\SubscriptionEntity $entity */
    $form = parent::buildForm($form, $form_state);

    return $form;
  }


  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);


    $value = $form_state->getValue(['mail', 0, 'value']);
    if ($value !== '' && !\Drupal::service('email.validator')
        ->isValid($value)) {
      $form_state
        ->setError( $form, t('The email address %mail is not valid.', [
          '%mail' => $value,
        ]));
    }

    $storage = $this->entityTypeManager->getStorage('subscription_entity');
    foreach ($storage->loadMultiple() as $entity) {
      $email = $entity->getMail() ?? '';

    }
    if ($value === $email) {
      $form_state
        ->setError( $form, t('The email address %mail is already used.', [
          '%mail' => $value,
        ]));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;

    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Subscription entity.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Subscription entity.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.subscription_entity.canonical', ['subscription_entity' => $entity->id()]);
  }

}
