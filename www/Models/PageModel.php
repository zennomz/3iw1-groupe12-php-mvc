<?php

namespace App\Models;

class PageModel
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function createPage(string $title, string $content, int $authorId)
    {
        $sql = 'INSERT INTO public."page" (title, content, author_id, date_created, slug) VALUES (:title, :content, :author_id, NOW(), :slug)';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'title' => $title,
            'content' => $content,
            'author_id' => $authorId,
            'slug' => $title
        ]);
    }

    public function getAllPages()
    {
        $sql = 'SELECT page.id, page.title, page.content, page.author_id, page.date_created, page.date_updated, page.slug, "user".username AS author_name FROM public."page" JOIN public."user" ON page.author_id = "user".id ORDER BY page.id ASC';
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getPageBySlug(string $slug)
    {
        $sql = 'SELECT page.id, page.title, page.content, page.author_id, page.slug, page.date_created, page.date_updated, "user".username AS author_name FROM public."page" JOIN public."user" ON page.author_id = "user".id WHERE page.slug = :slug';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function updatePage(int $pageId, string $title, string $content)
    {
        $sql = 'UPDATE public."page" SET title = :title, content = :content, date_updated = NOW() WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'title' => $title,
            'content' => $content,
            'id' => $pageId
        ]);
    }

    public function deletePage(int $pageId)
    {
        $sql = 'DELETE FROM public."page" WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $pageId]);
    }
}