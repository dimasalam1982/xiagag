<?php

namespace app\models;

/**
 * class Question
 *
 * @OA\Schema(@OA\Xml(name="Question"))
 */

use app\database\Database;
use app\models\Variant;
use app\models\BaseModel;

class Question extends BaseModel
{
    protected $id;

    protected $title;

    protected $uid;

    protected $variants = [];

    public function __construct($title = null)
    {
        parent::__construct();

        $this->title = $title;

        $this->uid = uniqid(time());
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setId($id)
    {
        $this->id = $id;

        $this->setQuestionIdForAllVariants();
    }

    public function getId()
    {
        return $this->id;
    }

    public function addVariant(Variant $variant): self
    {
        $this->variants[] = $variant;

        return $this;
    }

    public function validate()
    {
        parent::validate();

        return true;
    }

    /**
     * Deep saving with nested variants which save too
     *
     * @return $this|null
     */
    public function save(): ?self
    {
        if (!$this->validate()) {
            return null;
        }

        if (!$this->id) {
            $insertId = $this->db->insert('INSERT INTO question SET title=:title, uid=:uid',
                [
                    'title' => $this->title,
                    'uid' => $this->uid
                ]);

            if ($insertId>0){
                $this->setId($insertId);
                $this->saveVariants();
                return $this;
            }
        }

        return null;
    }

    private function setQuestionIdForAllVariants()
    {
        foreach ($this->variants as $variant) {
            $variant->setQuestionId($this->id);
        }
    }

    private function saveVariants(): ?self
    {
        if (!is_array($this->variants)) {
            return null;
        }

        foreach ($this->variants as $variant) {
            $variant->save();
        }

        return $this;
    }

    /**
     * Unique question URL
     *
     * @return string|null
     */
    public function getQuestionUrl(): ?string
    {
        return $this->uid
            ? getenv('BASE_URL') . '/question/show/' . $this->uid
            : null;
    }

    /**
     * Return null if validating is failed
     *
     * @return array|null
     */
    public function toArray(): ?array
    {

        if (!$this->validate()) {
            return null;
        }

        $variants = [];

        foreach ($this->variants as $variant) {
            $variants[] = $variant->toArray();
        }

        return [
            'id' => $this->id,
            'uid' => $this->uid,
            'url' => $this->getQuestionUrl(),
            'title' => $this->title,
            'variants' => $variants
        ];
    }

    public static function findByUid($uid): ?self
    {
        $data = (Database::getInstance())->getRow('SELECT * FROM question WHERE uid=:uid', ['uid' => $uid]);

        $question = null;

        if ($data){
            $question = new self;
            $question->id = $data['id'];
            $question->title = $data['title'];
            $question->uid = $data['uid'];
        }

        return $question;
    }

    /**
     * Load real answers for question from DB
     */
    public function loadVariants()
    {
        $variants = $this->id ? Variant::findByQuestionId($this->id) : [];

        foreach ($variants as $variant) {
            $this->addVariant($variant);
        }

    }

    /**
     * Find required answer by it ID in current question
     *
     * @param $variantId
     * @return \app\models\Variant|null
     */
    public function getVariantById($variantId): ?Variant
    {
        foreach ($this->variants as $variant) {
            if ($variant->getId() == $variantId) {
                return $variant;
            }
        }

        return null;
    }

    public function getVariants(): array
    {
        return $this->variants;
    }

}