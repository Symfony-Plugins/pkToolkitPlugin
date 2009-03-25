<?php

class pkZendSearch
{
  static public function getLuceneIndex(Doctrine_Table $table)
  {
    ProjectConfiguration::registerZend();
   
    if (file_exists($index = $table->getLuceneIndexFile()))
    {
      return Zend_Search_Lucene::open($index);
    }
    else
    {
      // Since we're using a subdir for all zend indexes to keep things
      // neat, we might need to make that subdir
      $parent = dirname($index);
      if (!file_exists($parent))
      {
        mkdir($parent);
      }
      
      return Zend_Search_Lucene::create($index);
    }
  }
   
  static public function getLuceneIndexFile(Doctrine_Table $table)
  {
    return sfConfig::get('sf_data_dir').'/zendIndexes/'.$table->getOption('name').'.'.sfConfig::get('sf_environment').'.index';
  }

  // Returns just the IDs.
  static public function searchLucene(Doctrine_Table $table, $luceneQuery)
  {
    $index = $table->getLuceneIndex();
    
    $hits = $index->find($luceneQuery);
   
    $ids = array();
    foreach ($hits as $hit)
    {
      $ids[] = $hit->pk;
    }

    return $ids;
  }
  
  static public function addSearchQuery(Doctrine_Table $table, Doctrine_Query $q = null, $luceneQuery)
  {
    $name = $table->getOption('name');

    if (is_null($q))
    {
      $q = Doctrine_Query::create()
        ->from($name);
    }
    
    $results = $table->searchLucene($luceneQuery);
    
    if (count($results))
    {
      $alias = $q->getRootAlias();
      // Contrary to Jobeet the above is NOT enough, the results will
      // not be in Lucene result order without what is usually referred
      // to as ORDER BY FIELD. Doctrine doesn't like FIELD in an
      // ORDER BY clause. However FIELD turns out to be a perfectly
      // ordinary MySQL function so you can put it in a SELECT alias
      // and then ORDER BY the alias (thanks John Wage).
      $q->select($alias.'.*, ' .
        'FIELD('.$alias.'.id, ' . implode(", ", $results) . ') AS field');
      $q->whereIn($alias.'.id', $results);
      $q->orderBy("field");
    }
    else
    {
      // Don't just let everything through when there are no hits!
      $q->andWhere('false');
    }
    
    return $q;
  }

  static public function rebuildLuceneIndex(Doctrine_Table $table)
  {
    $file = $table->getLuceneIndexFile();

    if (file_exists($file))
    {
      sfToolkit::clearDirectory($file);
      rmdir($file);
    }

    $index = $table->getLuceneIndex();
    
    // TODO: hydrate these one at a time once Doctrine supports
    // doing that efficiently
    $all = $table->findAll();
    foreach ($all as $item)
    {
      $item->updateLuceneIndex();
    }

    return $table->optimizeLuceneIndex();
  }
  
  static public function optimizeLuceneIndex(Doctrine_Table $table)
  {
    $index = $table->getLuceneIndex();

    return $index->optimize();
  }
  
  static public function updateLuceneIndex(Doctrine_Record $object, $fields = array())
  {
    $index = $object->getTable()->getLuceneIndex();
   
    // remove an existing entry
    if ($hit = $index->find('pk:'.$object->getId()))
    {
      $index->delete($hit->pk);
    }
   
    $doc = new Zend_Search_Lucene_Document();
   
    // store item id so we can retrieve the corresponding object
    $doc->addField(Zend_Search_Lucene_Field::UnIndexed('pk', $object->getId()));
    // index the fields
    foreach ($fields as $key => $value)
    {
      $doc->addField(Zend_Search_Lucene_Field::UnStored($key, $value, 'utf-8'));
    }
   
    // add item to the index
    $index->addDocument($doc);
    $index->commit();
  }
}

?>