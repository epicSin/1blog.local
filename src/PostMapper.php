<?php

namespace Blog;

use PDO;

class PostMapper
{
    private $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function getByUrlKey(string $urlKey): array
    {
        this->connection->prepare("SELECT * FROM posts WHERE url_key = :url_key");
        $statement->execute(['url_key' => $urlKey]);
        $result= $statement->fetchAll();
        return array_shift($result);
    }
}