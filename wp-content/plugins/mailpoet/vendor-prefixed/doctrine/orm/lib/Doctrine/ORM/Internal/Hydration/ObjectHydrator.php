<?php
 namespace MailPoetVendor\Doctrine\ORM\Internal\Hydration; if (!defined('ABSPATH')) exit; use MailPoetVendor\Doctrine\ORM\UnitOfWork; use PDO; use MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadata; use MailPoetVendor\Doctrine\ORM\PersistentCollection; use MailPoetVendor\Doctrine\ORM\Query; use MailPoetVendor\Doctrine\Common\Collections\ArrayCollection; use MailPoetVendor\Doctrine\ORM\Proxy\Proxy; class ObjectHydrator extends \MailPoetVendor\Doctrine\ORM\Internal\Hydration\AbstractHydrator { private $identifierMap = []; private $resultPointers = []; private $idTemplate = []; private $resultCounter = 0; private $rootAliases = []; private $initializedCollections = []; private $existingCollections = []; protected function prepare() { if (!isset($this->_hints[\MailPoetVendor\Doctrine\ORM\UnitOfWork::HINT_DEFEREAGERLOAD])) { $this->_hints[\MailPoetVendor\Doctrine\ORM\UnitOfWork::HINT_DEFEREAGERLOAD] = \true; } foreach ($this->_rsm->aliasMap as $dqlAlias => $className) { $this->identifierMap[$dqlAlias] = []; $this->idTemplate[$dqlAlias] = ''; if (!isset($this->_rsm->relationMap[$dqlAlias])) { continue; } $parent = $this->_rsm->parentAliasMap[$dqlAlias]; if (!isset($this->_rsm->aliasMap[$parent])) { throw \MailPoetVendor\Doctrine\ORM\Internal\Hydration\HydrationException::parentObjectOfRelationNotFound($dqlAlias, $parent); } $sourceClassName = $this->_rsm->aliasMap[$parent]; $sourceClass = $this->getClassMetadata($sourceClassName); $assoc = $sourceClass->associationMappings[$this->_rsm->relationMap[$dqlAlias]]; $this->_hints['fetched'][$parent][$assoc['fieldName']] = \true; if ($assoc['type'] === \MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadata::MANY_TO_MANY) { continue; } if ($assoc['mappedBy']) { $this->_hints['fetched'][$dqlAlias][$assoc['mappedBy']] = \true; continue; } if ($assoc['inversedBy']) { $class = $this->getClassMetadata($className); $inverseAssoc = $class->associationMappings[$assoc['inversedBy']]; if (!($inverseAssoc['type'] & \MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadata::TO_ONE)) { continue; } $this->_hints['fetched'][$dqlAlias][$inverseAssoc['fieldName']] = \true; } } } protected function cleanup() { $eagerLoad = isset($this->_hints[\MailPoetVendor\Doctrine\ORM\UnitOfWork::HINT_DEFEREAGERLOAD]) && $this->_hints[\MailPoetVendor\Doctrine\ORM\UnitOfWork::HINT_DEFEREAGERLOAD] == \true; parent::cleanup(); $this->identifierMap = $this->initializedCollections = $this->existingCollections = $this->resultPointers = []; if ($eagerLoad) { $this->_uow->triggerEagerLoads(); } $this->_uow->hydrationComplete(); } protected function hydrateAllData() { $result = []; while ($row = $this->_stmt->fetch(\PDO::FETCH_ASSOC)) { $this->hydrateRowData($row, $result); } foreach ($this->initializedCollections as $coll) { $coll->takeSnapshot(); } return $result; } private function initRelatedCollection($entity, $class, $fieldName, $parentDqlAlias) { $oid = \spl_object_hash($entity); $relation = $class->associationMappings[$fieldName]; $value = $class->reflFields[$fieldName]->getValue($entity); if ($value === null || \is_array($value)) { $value = new \MailPoetVendor\Doctrine\Common\Collections\ArrayCollection((array) $value); } if (!$value instanceof \MailPoetVendor\Doctrine\ORM\PersistentCollection) { $value = new \MailPoetVendor\Doctrine\ORM\PersistentCollection($this->_em, $this->_metadataCache[$relation['targetEntity']], $value); $value->setOwner($entity, $relation); $class->reflFields[$fieldName]->setValue($entity, $value); $this->_uow->setOriginalEntityProperty($oid, $fieldName, $value); $this->initializedCollections[$oid . $fieldName] = $value; } else { if (isset($this->_hints[\MailPoetVendor\Doctrine\ORM\Query::HINT_REFRESH]) || isset($this->_hints['fetched'][$parentDqlAlias][$fieldName]) && !$value->isInitialized()) { $value->setDirty(\false); $value->setInitialized(\true); $value->unwrap()->clear(); $this->initializedCollections[$oid . $fieldName] = $value; } else { $this->existingCollections[$oid . $fieldName] = $value; } } return $value; } private function getEntity(array $data, $dqlAlias) { $className = $this->_rsm->aliasMap[$dqlAlias]; if (isset($this->_rsm->discriminatorColumns[$dqlAlias])) { $fieldName = $this->_rsm->discriminatorColumns[$dqlAlias]; if (!isset($this->_rsm->metaMappings[$fieldName])) { throw \MailPoetVendor\Doctrine\ORM\Internal\Hydration\HydrationException::missingDiscriminatorMetaMappingColumn($className, $fieldName, $dqlAlias); } $discrColumn = $this->_rsm->metaMappings[$fieldName]; if (!isset($data[$discrColumn])) { throw \MailPoetVendor\Doctrine\ORM\Internal\Hydration\HydrationException::missingDiscriminatorColumn($className, $discrColumn, $dqlAlias); } if ($data[$discrColumn] === "") { throw \MailPoetVendor\Doctrine\ORM\Internal\Hydration\HydrationException::emptyDiscriminatorValue($dqlAlias); } $discrMap = $this->_metadataCache[$className]->discriminatorMap; $discriminatorValue = (string) $data[$discrColumn]; if (!isset($discrMap[$discriminatorValue])) { throw \MailPoetVendor\Doctrine\ORM\Internal\Hydration\HydrationException::invalidDiscriminatorValue($discriminatorValue, \array_keys($discrMap)); } $className = $discrMap[$discriminatorValue]; unset($data[$discrColumn]); } if (isset($this->_hints[\MailPoetVendor\Doctrine\ORM\Query::HINT_REFRESH_ENTITY]) && isset($this->rootAliases[$dqlAlias])) { $this->registerManaged($this->_metadataCache[$className], $this->_hints[\MailPoetVendor\Doctrine\ORM\Query::HINT_REFRESH_ENTITY], $data); } $this->_hints['fetchAlias'] = $dqlAlias; return $this->_uow->createEntity($className, $data, $this->_hints); } private function getEntityFromIdentityMap($className, array $data) { $class = $this->_metadataCache[$className]; if ($class->isIdentifierComposite) { $idHash = ''; foreach ($class->identifier as $fieldName) { $idHash .= ' ' . (isset($class->associationMappings[$fieldName]) ? $data[$class->associationMappings[$fieldName]['joinColumns'][0]['name']] : $data[$fieldName]); } return $this->_uow->tryGetByIdHash(\ltrim($idHash), $class->rootEntityName); } else { if (isset($class->associationMappings[$class->identifier[0]])) { return $this->_uow->tryGetByIdHash($data[$class->associationMappings[$class->identifier[0]]['joinColumns'][0]['name']], $class->rootEntityName); } } return $this->_uow->tryGetByIdHash($data[$class->identifier[0]], $class->rootEntityName); } protected function hydrateRowData(array $row, array &$result) { $id = $this->idTemplate; $nonemptyComponents = []; $rowData = $this->gatherRowData($row, $id, $nonemptyComponents); $this->resultPointers = []; foreach ($rowData['data'] as $dqlAlias => $data) { $entityName = $this->_rsm->aliasMap[$dqlAlias]; if (isset($this->_rsm->parentAliasMap[$dqlAlias])) { $parentAlias = $this->_rsm->parentAliasMap[$dqlAlias]; $path = $parentAlias . '.' . $dqlAlias; if (!isset($nonemptyComponents[$parentAlias])) { continue; } $parentClass = $this->_metadataCache[$this->_rsm->aliasMap[$parentAlias]]; $relationField = $this->_rsm->relationMap[$dqlAlias]; $relation = $parentClass->associationMappings[$relationField]; $reflField = $parentClass->reflFields[$relationField]; if ($this->_rsm->isMixed && isset($this->rootAliases[$parentAlias])) { $objectClass = $this->resultPointers[$parentAlias]; $parentObject = $objectClass[\key($objectClass)]; } else { if (isset($this->resultPointers[$parentAlias])) { $parentObject = $this->resultPointers[$parentAlias]; } else { $element = $this->getEntity($data, $dqlAlias); $this->resultPointers[$dqlAlias] = $element; $rowData['data'][$parentAlias][$relationField] = $element; unset($this->_hints['fetched'][$parentAlias][$relationField]); continue; } } $oid = \spl_object_hash($parentObject); if (!($relation['type'] & \MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadata::TO_ONE)) { $reflFieldValue = $reflField->getValue($parentObject); if (isset($nonemptyComponents[$dqlAlias])) { $collKey = $oid . $relationField; if (isset($this->initializedCollections[$collKey])) { $reflFieldValue = $this->initializedCollections[$collKey]; } else { if (!isset($this->existingCollections[$collKey])) { $reflFieldValue = $this->initRelatedCollection($parentObject, $parentClass, $relationField, $parentAlias); } } $indexExists = isset($this->identifierMap[$path][$id[$parentAlias]][$id[$dqlAlias]]); $index = $indexExists ? $this->identifierMap[$path][$id[$parentAlias]][$id[$dqlAlias]] : \false; $indexIsValid = $index !== \false ? isset($reflFieldValue[$index]) : \false; if (!$indexExists || !$indexIsValid) { if (isset($this->existingCollections[$collKey])) { if ($element = $this->getEntityFromIdentityMap($entityName, $data)) { $this->resultPointers[$dqlAlias] = $element; } else { unset($this->resultPointers[$dqlAlias]); } } else { $element = $this->getEntity($data, $dqlAlias); if (isset($this->_rsm->indexByMap[$dqlAlias])) { $indexValue = $row[$this->_rsm->indexByMap[$dqlAlias]]; $reflFieldValue->hydrateSet($indexValue, $element); $this->identifierMap[$path][$id[$parentAlias]][$id[$dqlAlias]] = $indexValue; } else { $reflFieldValue->hydrateAdd($element); $reflFieldValue->last(); $this->identifierMap[$path][$id[$parentAlias]][$id[$dqlAlias]] = $reflFieldValue->key(); } $this->resultPointers[$dqlAlias] = $element; } } else { $this->resultPointers[$dqlAlias] = $reflFieldValue[$index]; } } else { if (!$reflFieldValue) { $this->initRelatedCollection($parentObject, $parentClass, $relationField, $parentAlias); } else { if ($reflFieldValue instanceof \MailPoetVendor\Doctrine\ORM\PersistentCollection && $reflFieldValue->isInitialized() === \false) { $reflFieldValue->setInitialized(\true); } } } } else { $reflFieldValue = $reflField->getValue($parentObject); if (!$reflFieldValue || isset($this->_hints[\MailPoetVendor\Doctrine\ORM\Query::HINT_REFRESH]) || $reflFieldValue instanceof \MailPoetVendor\Doctrine\ORM\Proxy\Proxy && !$reflFieldValue->__isInitialized__) { if (isset($nonemptyComponents[$dqlAlias])) { $element = $this->getEntity($data, $dqlAlias); $reflField->setValue($parentObject, $element); $this->_uow->setOriginalEntityProperty($oid, $relationField, $element); $targetClass = $this->_metadataCache[$relation['targetEntity']]; if ($relation['isOwningSide']) { if ($relation['inversedBy']) { $inverseAssoc = $targetClass->associationMappings[$relation['inversedBy']]; if ($inverseAssoc['type'] & \MailPoetVendor\Doctrine\ORM\Mapping\ClassMetadata::TO_ONE) { $targetClass->reflFields[$inverseAssoc['fieldName']]->setValue($element, $parentObject); $this->_uow->setOriginalEntityProperty(\spl_object_hash($element), $inverseAssoc['fieldName'], $parentObject); } } else { if ($parentClass === $targetClass && $relation['mappedBy']) { $targetClass->reflFields[$relationField]->setValue($element, $parentObject); } } } else { $targetClass->reflFields[$relation['mappedBy']]->setValue($element, $parentObject); $this->_uow->setOriginalEntityProperty(\spl_object_hash($element), $relation['mappedBy'], $parentObject); } $this->resultPointers[$dqlAlias] = $element; } else { $this->_uow->setOriginalEntityProperty($oid, $relationField, null); $reflField->setValue($parentObject, null); } } else { $this->resultPointers[$dqlAlias] = $reflFieldValue; } } } else { $this->rootAliases[$dqlAlias] = \true; $entityKey = $this->_rsm->entityMappings[$dqlAlias] ?: 0; if (!isset($nonemptyComponents[$dqlAlias])) { if ($this->_rsm->isMixed) { $result[] = [$entityKey => null]; } else { $result[] = null; } $resultKey = $this->resultCounter; ++$this->resultCounter; continue; } if (!isset($this->identifierMap[$dqlAlias][$id[$dqlAlias]])) { $element = $this->getEntity($data, $dqlAlias); if ($this->_rsm->isMixed) { $element = [$entityKey => $element]; } if (isset($this->_rsm->indexByMap[$dqlAlias])) { $resultKey = $row[$this->_rsm->indexByMap[$dqlAlias]]; if (isset($this->_hints['collection'])) { $this->_hints['collection']->hydrateSet($resultKey, $element); } $result[$resultKey] = $element; } else { $resultKey = $this->resultCounter; ++$this->resultCounter; if (isset($this->_hints['collection'])) { $this->_hints['collection']->hydrateAdd($element); } $result[] = $element; } $this->identifierMap[$dqlAlias][$id[$dqlAlias]] = $resultKey; $this->resultPointers[$dqlAlias] = $element; } else { $index = $this->identifierMap[$dqlAlias][$id[$dqlAlias]]; $this->resultPointers[$dqlAlias] = $result[$index]; $resultKey = $index; } } if (isset($this->_hints[\MailPoetVendor\Doctrine\ORM\Query::HINT_INTERNAL_ITERATION]) && $this->_hints[\MailPoetVendor\Doctrine\ORM\Query::HINT_INTERNAL_ITERATION]) { $this->_uow->hydrationComplete(); } } if (!isset($resultKey)) { $this->resultCounter++; } if (isset($rowData['scalars'])) { if (!isset($resultKey)) { $resultKey = isset($this->_rsm->indexByMap['scalars']) ? $row[$this->_rsm->indexByMap['scalars']] : $this->resultCounter - 1; } foreach ($rowData['scalars'] as $name => $value) { $result[$resultKey][$name] = $value; } } if (isset($rowData['newObjects'])) { if (!isset($resultKey)) { $resultKey = $this->resultCounter - 1; } $scalarCount = isset($rowData['scalars']) ? \count($rowData['scalars']) : 0; foreach ($rowData['newObjects'] as $objIndex => $newObject) { $class = $newObject['class']; $args = $newObject['args']; $obj = $class->newInstanceArgs($args); if ($scalarCount == 0 && \count($rowData['newObjects']) == 1) { $result[$resultKey] = $obj; continue; } $result[$resultKey][$objIndex] = $obj; } } } public function onClear($eventArgs) { parent::onClear($eventArgs); $aliases = \array_keys($this->identifierMap); $this->identifierMap = \array_fill_keys($aliases, []); } } 