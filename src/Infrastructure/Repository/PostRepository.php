<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Contract\MenuSlugProviderInterface;
use App\Domain\Entity\Post;
use App\Domain\Repository\CategoryRepositoryInterface;
use App\Domain\Repository\PostRepositoryInterface;
use App\Domain\Repository\UserRepositoryInterface;
use App\Infrastructure\Persistence\AbstractRepository;

class PostRepository extends AbstractRepository implements PostRepositoryInterface, MenuSlugProviderInterface
{
    protected string $table = 'posts';
    protected string $entityClass = Post::class;

    public function __construct(
        private ?CategoryRepositoryInterface $categoryRepository = null,
        private ?UserRepositoryInterface $userRepository = null,
    ) {
        $this->categoryRepository ??= new CategoryRepository();
        $this->userRepository ??= new UserRepository();
    }

    public function find(int $id): ?Post
    {
        /** @var Post|null $post */
        $post = $this->findOneRaw($id);
        return $post ? $this->loadRelations($post) : null;
    }

    /** @return Post[] */
    public function findAll(): array
    {
        /** @var Post[] $posts */
        $posts = $this->findAllRaw();
        return $this->loadRelationsBatch($posts);
    }

    public function findBySlug(string $slug): ?Post
    {
        /** @var Post|null $post */
        $post = $this->findOneBy('slug', $slug);
        return $post ? $this->loadRelations($post) : null;
    }

    /** @return Post[] */
    public function findByCategory(int $categoryId): array
    {
        /** @var Post[] $posts */
        $posts = $this->findManyBy('category_id', $categoryId);
        usort($posts, fn($a, $b) => $b->getId() <=> $a->getId());
        return $this->loadRelationsBatch($posts);
    }

    /** @return Post[] */
    public function findMenuItems(): array
    {
        $stmt = $this->db()->prepare(
            'SELECT * FROM ' . $this->table . ' WHERE is_in_menu = 1 ORDER BY menu_order ASC'
        );
        $stmt->execute();
        /** @var Post[] $posts */
        $posts = array_map(fn($row) => $this->hydrate($row), $stmt->fetchAll());
        return $this->loadRelationsBatch($posts);
    }

    public function save(Post $post): bool
    {
        return $this->persist($post);
    }

    public function delete(Post $post): bool
    {
        return $this->remove($post);
    }

    /** @return array<int, int> */
    public function countByCategory(): array
    {
        $stmt = $this->db()->query(
            'SELECT category_id, COUNT(*) AS c FROM ' . $this->table
            . ' WHERE category_id IS NOT NULL GROUP BY category_id'
        );
        $out = [];
        foreach ($stmt->fetchAll() as $row) {
            $out[(int) $row['category_id']] = (int) $row['c'];
        }
        return $out;
    }

    public function getMenuSlugs(): array
    {
        return array_map(
            fn(Post $post) => [
                'slug' => $post->getSlug(),
                'title' => $post->getTitle(),
                'type' => 'post',
            ],
            $this->findMenuItems(),
        );
    }

    protected function extractProps(object $entity): array
    {
        $props = parent::extractProps($entity);
        unset($props['user'], $props['category']);
        return $props;
    }

    private function loadRelations(Post $post): Post
    {
        if ($post->getCategoryId() !== null) {
            $post->setCategory($this->categoryRepository->find($post->getCategoryId()));
        }
        $post->setUser($this->userRepository->find($post->getUserId()));
        return $post;
    }

    /**
     * @param Post[] $posts
     * @return Post[]
     */
    private function loadRelationsBatch(array $posts): array
    {
        if (empty($posts)) {
            return $posts;
        }

        $categoryIds = array_unique(array_filter(array_map(fn(Post $p) => $p->getCategoryId(), $posts)));
        $userIds = array_unique(array_map(fn(Post $p) => $p->getUserId(), $posts));

        $categories = [];
        foreach ($categoryIds as $cid) {
            $cat = $this->categoryRepository->find($cid);
            if ($cat) {
                $categories[$cid] = $cat;
            }
        }
        $users = [];
        foreach ($userIds as $uid) {
            $u = $this->userRepository->find($uid);
            if ($u) {
                $users[$uid] = $u;
            }
        }

        foreach ($posts as $post) {
            $cid = $post->getCategoryId();
            if ($cid !== null && isset($categories[$cid])) {
                $post->setCategory($categories[$cid]);
            }
            if (isset($users[$post->getUserId()])) {
                $post->setUser($users[$post->getUserId()]);
            }
        }

        return $posts;
    }
}
