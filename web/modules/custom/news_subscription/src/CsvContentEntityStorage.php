<?php

namespace Drupal\news_subscription;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Cache\MemoryCache\MemoryCacheInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\ContentEntityStorageBase;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\StreamWrapper\PublicStream;

class CsvContentEntityStorage extends ContentEntityStorageBase {

  /**
   * @var string
   */
  protected $filename;

  /**
   * @var array
   */
  protected $data = [];

  /**
   * @var array
   */
  protected $fields;

  public function __construct(
    EntityTypeInterface $entity_type,
    EntityFieldManagerInterface $entity_field_manager,
    CacheBackendInterface $cache,
    MemoryCacheInterface $memory_cache = NULL,
    EntityTypeBundleInfoInterface $entity_type_bundle_info = NULL
  ) {
    parent::__construct($entity_type, $entity_field_manager, $cache, $memory_cache, $entity_type_bundle_info);
    $this->fields = $fields = array_keys($this->entityFieldManager->getBaseFieldDefinitions($this->entityTypeId));
    $this->filename = PublicStream::basePath() . '/' . $this->entityTypeId . '.csv';
    $this->loadDataFromCsv();
  }

  /**
   * Loads data from CSV file.
   */
  protected function loadDataFromCsv() {
    $content = @file_get_contents($this->filename);
    if (!$content) {
      return;
    }

    foreach (explode("\n", $content) as $line => $row) {
      // Skip first line in file.
      if ($line === 0) {
        continue;
      }
      $values = explode(',', $row);
      $id = $values[0];
      $this->data[$id] = $values;
    }
  }

  /**
   * Saves data to CSV file.
   */
  protected function saveDataToCsv() {
    // Add field names to first line in file.
    $content = implode(',', $this->fields);
    foreach ($this->data as $row) {
      $content .= "\n" . implode(',', $row);
    }
    @file_put_contents($this->filename, $content);
  }

  /**
   * {@inheritdoc}
   */
  protected function getQueryServiceName() {
    return 'entity.query.null';
  }

  /**
   * {@inheritdoc}
   */
  protected function has($id, EntityInterface $entity) {
    if ($id > 0) {
      return !empty($this->data[$id]);
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  protected function doLoadMultiple(array $ids = NULL) {
    $entities = [];

    foreach ($this->data as $id => $row) {
      if (!$ids || in_array($id, $ids)) {
        $entity = new $this->entityClass([], $this->entityTypeId);
        $values = array_combine($this->fields, $row);
        if ($values) {
          $this->initFieldValues($entity, $values);
          $entities[$id] = $entity;
        }
      }
    }

    return $entities;
  }

  /**
   * {@inheritdoc}
   */
  protected function doSaveFieldItems(ContentEntityInterface $entity, array $names = []) {
    $values = [];
    foreach ($this->fields as $field) {
      $value = $entity->get($field)->getString();
      // Clean all commas in data.
      $values[$field] = str_replace(',', '', $value);
    }
    if (!$values) {
      return;
    }

    $id = $entity->id();
    if ($id === NULL) {
      $ids = array_keys($this->data) ?: [0];
      $id = max($ids) + 1; // Gets next ID.
      $values['id'] = $id;
      $entity->set('id', $id);
    }
    $this->data[$id] = $values;
    $this->saveDataToCsv();
  }

  /**
   * @inheritDoc
   */
  protected function doDeleteFieldItems($entities) {
    foreach ($entities as $entity) {
      $id = $entity->id();
      unset($this->data[$id]);
    }
    $this->saveDataToCsv();
  }

  /**
   * {@inheritdoc}
   */
  protected function doDeleteRevisionFieldItems(ContentEntityInterface $revision) {}

  /**
   * {@inheritdoc}
   */
  protected function doLoadRevisionFieldItems($revision_id) {}

  /**
   * {@inheritdoc}
   */
  protected function purgeFieldItems(ContentEntityInterface $entity, FieldDefinitionInterface $field_definition) {}

  /**
   * {@inheritdoc}
   */
  protected function readFieldItemsToPurge(FieldDefinitionInterface $field_definition, $batch_size) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function countFieldData($storage_definition, $as_bool = FALSE) {
    return $as_bool ? FALSE : 0;
  }

}
