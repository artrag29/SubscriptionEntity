<?php

namespace Drupal\news_subscription\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class SubscribeForm.
 */
class SubscribeForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'subscribe_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#description' => $this->t('Subscriber name'),
      '#maxlength' => 120,
      '#size' => 64,
      '#weight' => '0',
      '#required' => TRUE,
    ];
    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email'),
      '#description' => $this->t('Subscriber email'),
      '#weight' => '0',
      '#required' => TRUE,
    ];

    $news_vocab = \Drupal::entityManager()->getStorage('taxonomy_term')->loadTree('news_categories');

    foreach ($news_vocab as $term) {
      $term_data[$term->tid] = $term->name;
    }
    $form['category'] = [
      '#type' => 'select',
      '#empty_option' => 'Select',
      '#title' => t('Select category'),
      '#options' => $term_data ?? '',
      '#size' => 1,
      '#weight' => '0',
      '#required' => TRUE,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    foreach ($form_state->getValues() as $key => $value) {
      // @TODO: Validate fields.
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Display result.
    foreach ($form_state->getValues() as $key => $value) {
      \Drupal::messenger()->addMessage($key . ': ' . ($key === 'text_format'?$value['value']:$value));
    }
  }

}
