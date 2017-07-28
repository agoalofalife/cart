<?php
declare(strict_types=1);

namespace Cart\Drivers;

use Cart\Contracts\CartDriverContract;
use Cart\CountOperation\AdditionCount;
use Cart\CountOperation\ChangeCount;
use Cart\Traits\Validate;
use Predis\Client;

/**
 * Class RedisDriver
 *
 * @package Cart\Drivers
 */
class RedisDriver implements CartDriverContract
{
    /**
     * @var string
     */
    protected $prefix = 'cart';

    use Validate;

    /**
     * @var Client
     */
    protected $redis;

    public function __construct(Client $client)
    {
        $this->redis = $client;
        $this->prefix = config('app.drivers.redis.prefix');
    }

    /**
     * Add new item in cart
     *
     * @param array $item
     * @return mixed
     */
    public function add(array $item): bool
    {
        if ($this->validate($item, ['id', 'user_id']) === false) {
            return false;
        }

        if ($this->existUser((int)$item['user_id'])) {
            if ($this->existItem((int)$item['id'], (int)$item['user_id'])) {
                call_user_func([$this, 'incrementItem'], $item);
            } else {
                call_user_func([$this, 'addItem'], $item);
            }
        } else {
            $item['count'] = 1;
            $this->addRow($item);
        }

        return true;
    }

    /**
     * Remove item from cart
     *
     * @param array $item
     * @return bool|mixed
     * @internal param array $itemId
     */
    public function remove(array $item): bool
    {
        if ($this->validate($item, ['id', 'user_id']) === false) {
            return false;
        }

        $this->redis->hdel($this->normalizeKey((int)$item['user_id']), $item['id']);
        return true;
    }

    /**
     * Clear cart for concrete relation entity
     *
     * @param int $entityId
     * @return mixed|void
     */
    public function clear(int $entityId): void
    {
        $this->redis->del($this->normalizeKey($entityId));
    }

    /**
     * Change item(position)
     *
     * @param array $item
     * @return mixed
     */
    public function change(array $item): bool
    {
        if ($this->validate($item, ['id', 'user_id', 'count']) === false) {
            return false;
        }

        app()->bind(ChangeCount::class, ChangeCount::class);
        $itemFromRedis = $this->redis->hget($this->normalizeKey((int)$item['user_id']), $item['id']);

        if (!is_null($itemFromRedis)) {
            $itemFromRedis = unserialize($itemFromRedis);
        } else {
            return false;
        }

        $item['count'] = app()->make(ChangeCount::class)->execute((int)$itemFromRedis['count'], $item['count']);
        $this->addRow($item);
        return true;
    }

    /**
     * @param array $item
     */
    private function incrementItem(array $item) : void
    {
        app()->bind(AdditionCount::class, AdditionCount::class);

        $itemFromRedis = unserialize($this->redis->hget($this->normalizeKey((int)$item['user_id']), $item['id']));

        $itemFromRedis['count'] = app()->make(AdditionCount::class)
                                        ->execute(1, (int)$itemFromRedis['count']);

        $this->addRow($itemFromRedis);
    }

    private function addItem(array $item) : void
    {
        $this->addRow($item);
    }

    /**
     * Check exist user in cart
     *
     * @param int $userId
     * @return bool
     */
    private function existUser(int $userId): bool
    {
        return count($this->redis->hgetall($this->normalizeKey($userId))) > 0;
    }

    /**
     * @param int $itemId
     * @param int $userId
     * @return bool
     */
    private function existItem(int $itemId, int $userId) : bool
    {
         return !is_null($this->redis->hget($this->normalizeKey($userId), $itemId));
    }

    /**
     * Normalize string : key redis
     * @param $userId
     * @return string
     */
    private function normalizeKey(int $userId) : string
    {
        return $userId . '.' . $this->prefix;
    }

    /**
     * @param array $item
     * @internal param int $userId
     * @internal param array $items
     */
    private function addRow(array $item) : void
    {
        if (isset($item['count']) && $item['count'] == 0) {
            $this->remove($item);
        } else {
            $this->redis->hset($this->normalizeKey((int)$item['user_id']), $item['id'], serialize($item));
        }
    }
}