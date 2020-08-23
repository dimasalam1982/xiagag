<?php

namespace app\models;

/**
 * class Variant
 *
 * @OA\Schema(@OA\Xml(name="Variant"))
 */

use app\database\Database;
use app\models\BaseModel;

class Variant extends BaseModel
{
    protected $id;

    /**
     * @OA\Property(example="10-36", description="Title of some answer to question")
     * @var string
     */
    protected $title;

    protected $questionId;

    public function __construct($params = [])
    {
        parent::__construct();

        $this->title = $params['title'] ?? '';
    }

    public function validate()
    {
        parent::validate();

        return true;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setQuestionId(int $questionId): self
    {
        $this->questionId = $questionId;
        return $this;
    }

    public function save(): ?self
    {
        if (!$this->validate()){
            return null;
        }

        if (!$this->questionId){
            return null;
        }

        if ($this->id) {
            return $this;
        }

        $id = $this->db->insert('INSERT INTO variant SET question_id=:question_id, title=:title', [
            'question_id' => $this->questionId,
            'title' => $this->title
        ]);

        $this->id = $id;

        return $this;
    }

    /**
     * Return null if validating is failed
     * @return array|null
     */
    public function toArray(): ?array
    {
        if (!$this->validate()){
            return null;
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'questionId' => $this->questionId
        ];
    }

    public static function findByQuestionId(int $questionId): array
    {
        $data = (Database::getInstance())
            ->getAll('SELECT * FROM variant WHERE question_id=:question_id',
                ['question_id' => $questionId]
            );

        $variants = [];

        foreach ($data as $item) {
            $variants[] = self::createFromRaw($item);
        }

        return $variants;
    }

    public static function createFromRaw(array $data): self
    {
        $variant = new self;
        $variant->id = $data['id'] ?? null;
        $variant->questionId = $data['question_id'] ?? null;
        $variant->title = $data['title'] ?? null;
        return $variant;
    }

    public static function findById($id): ?self
    {
        $data = (Database::getInstance())
            ->getRow('SELECT * FROM variant WHERE id=:id',
                ['id' => $id]
            );

        return !empty($data)
            ? self::createFromRaw($data)
            : null;
    }

}