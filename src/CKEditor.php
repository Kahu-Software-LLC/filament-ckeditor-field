<?php

namespace Kahusoftware\FilamentCkeditorField;

use Closure;
use Filament\Forms\Components\Component;

class CKEditor extends Component {
    protected string | Closure $content = '';

    protected string $name = 'ckeditor';

    protected string $view = 'filament-ckeditor-field::ckeditor';

    final public function __construct(string $name = 'ckeditor')
    {
        $this->name($name);
    }
 
    public static function make(string $name = 'ckeditor'): static
    {
        return app(static::class, ['name' => $name]);
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

    public function name(string $name): static
    {
        $this->name = $name;
 
        return $this;
    }

    public function getContent(): string
    {
        return $this->evaluate($this->content);
    }

    public function getName(): string
    {
        return $this->name;
    }
}