<?php

namespace Livewire;

use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;

abstract class LivewireComponent
{
    use Concerns\CanBeSerialized,
        Concerns\ValidatesInput;

    public $id;
    public $prefix;
    public $children = [];
    public $redirectTo;
    public $callOnParent;

    public function __construct($id, $prefix)
    {
        $this->id = $id;
        $this->prefix = $prefix;
    }

    abstract public function render();

    public function redirectTo($url)
    {
        $this->redirectTo = $url;
    }

    public function callOnParent($method)
    {
        $this->callOnParent = $method;
    }

    public function output($errors = null)
    {
        $dom = $this->render()->with([
            'errors' => (new ViewErrorBag)
                ->put('default', $errors ?: new MessageBag),
            'livewire' => $this,
        ])->render();

        // This allows us to recognize when a previosuly rendered child,
        // is no longer being rendered, we can clear their "children"
        // entry so that we don't still return dummy data.
        foreach ($this->children as $childName => $id) {
            if (! in_array($childName, $this->mountedChildren)) {
                unset($this->children[$childName]);
            }
        }

        return $dom;
    }

    public function getPropertyValue($prop) {
        // This is used by wrappers. Otherwise,
        // users would have to declare props as "public".
        return $this->{$prop};
    }
}
