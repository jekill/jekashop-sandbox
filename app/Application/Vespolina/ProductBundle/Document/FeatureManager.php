<?php

namespace Application\Vespolina\ProductBundle\Document;

use \Doctrine\ODM\MongoDB\DocumentManager;

class FeatureManager{

    private $class;
    private $dm;
    public function __construct(DocumentManager $dm, $featureClass)
    {
        $this->class = $featureClass;
        $this->dm = $dm;
        $this->repository = $dm->getRepository($featureClass);
    }

    public function getDocumentManager()
    {
        return $this->dm;
    }

    public function getRepository()
    {
        return $this->repository;
    }


    public function remove($feature)
    {
        $this->dm->remove($feature);
        $this->dm->flush();
    }

    public function findFeatureById($id)
    {
        return $this->repository->find($id);
    }

    public function updateFeature($feature,$flush=true)
    {
        $this->dm->persist($feature);
        if ($flush)
        {
            $this->dm->flush();
        }
    }

}