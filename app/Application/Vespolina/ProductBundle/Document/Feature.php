<?php
namespace Application\Vespolina\ProductBundle\Document;
use Vespolina\ProductBundle\Model\Feature\FeatureInterface;

class Feature implements  FeatureInterface{

    /**
     * @var MongoId $id
     */
    protected $id;

    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var string $type
     */
    protected $type;

    /**
     * @var string $searchTerm
     */
    protected $searchTerm;

    /**
     * @var int $pos
     */
    protected $pos;


    /**
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set type
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get type
     *
     * @return string $type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set searchTerm
     *
     * @param string $searchTerm
     */
    public function setSearchTerm($searchTerm)
    {
        $this->searchTerm = $searchTerm;
    }

    /**
     * Get searchTerm
     *
     * @return string $searchTerm
     */
    public function getSearchTerm()
    {
        return $this->searchTerm;
    }

    /**
     * Set pos
     *
     * @param int $pos
     */
    public function setPos($pos)
    {
        $this->pos = $pos;
    }

    /**
     * Get pos
     *
     * @return int $pos
     */
    public function getPos()
    {
        return $this->pos;
    }
}
