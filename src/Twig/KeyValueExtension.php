<?php

namespace App\Twig;

use Webmozart\KeyValueStore\Api\KeyValueStore;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class KeyValueExtension extends AbstractExtension
{
    protected $keyValueStore;

    public function __construct(KeyValueStore $keyValueStore)
    {
        $this->keyValueStore = $keyValueStore;
    }

    public function getName()
    {
        return 'dashboard_json_key_value_store_extension';
    }

    public function getFunctions()
    {
        return [
            'param' => new TwigFunction('param', [$this, 'param']),
        ];
    }

    public function param($name)
    {
        if (!$this->keyValueStore->exists($name)) {
            return 'undef key ' . $name;
        }

        $value = $this->keyValueStore->get($name);

        return !empty($value) ? $value : null;
    }
}
