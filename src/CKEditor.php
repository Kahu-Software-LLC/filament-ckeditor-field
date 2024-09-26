<?php

namespace Kahusoftware\FilamentCkeditorField;

use Closure;
use Filament\Forms\Components\Component;

class CKEditor extends Component {
    protected string | Closure $content = '';

    protected string $view = 'filament-ckeditor-field::ckeditor';

    final public function __construct()
    {

    }
 
    public static function make(): static
    {
        return app(static::class);
    }

    protected function setUp(): void
    {
        parent::setUp();
 
        $this->dehydrated(false);
    }

    public function content(string | Closure $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getContent(): string
    {
        return $this->evaluate($this->content);
    }
}