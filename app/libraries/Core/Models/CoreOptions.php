<?php

namespace ZCMS\Core\Models;

use Phalcon\Mvc\Model;
use ZCMS\Core\Cache\ZCache;

/**
 * Class CoreOptions
 *
 * @package ZCMS\Core\Models
 */
class CoreOptions extends Model
{
    /**
     * Cache options key
     */
    const ZCMS_CACHE_MODEL_CORE_OPTIONS = 'ZCMS_CACHE_MODEL_CORE_OPTIONS';

    /**
     * @var int
     */
    public $option_id;

    /**
     * @var string
     */
    public $option_scope;

    /**
     * @var string
     */
    public $option_name;

    /**
     * @var string
     */
    public $option_value;

    /**
     * If value equal 1 then option autoload to CACHE
     *
     * @var int Value in [0,1]
     */
    public $autoload;

    public static function initOrUpdateCacheOptions($reloadCache = false)
    {
        $cache = ZCache::getInstance(ZCMS_APPLICATION);
        //Load cache options
        $options = $cache->get(self::ZCMS_CACHE_MODEL_CORE_OPTIONS);

        //If reload cache Or current cache is null
        if ($reloadCache || $options === null) {
            $options = self::find([
                'columns' => ['option_scope', 'option_name', 'option_value'],
                'conditions' => 'autoload = 1'
            ])->toArray();
            $cache->save(self::ZCMS_CACHE_MODEL_CORE_OPTIONS, $options);
        }
    }

    /**
     * Execute before create
     */
    public function beforeCreate()
    {
        if ($this->autoload) {
            self::initOrUpdateCacheOptions(true);
        }
    }

    /**
     * Execute before update
     */
    public function beforeUpdate()
    {
        if ($this->autoload) {
            self::initOrUpdateCacheOptions(true);
        }
    }
}