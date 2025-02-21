<?php
namespace ShortLinks;

abstract class Singleton {
    private static array $instances = [];

    protected function __construct() {}

    public static function getInstance(): static {
        $class = static::class;
        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new static();
            if (method_exists(self::$instances[$class], 'init')) {
                self::$instances[$class]->init();
            }
        }
        return self::$instances[$class];
    }
}