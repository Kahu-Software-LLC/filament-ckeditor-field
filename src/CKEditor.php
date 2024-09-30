<?php

namespace Kahusoftware\FilamentCkeditorField;

use Closure;
use Filament\Forms\Components\Component;

class CKEditor extends Component {
    protected string | Closure $content = '';

    protected string $name = 'ckeditor';

    protected ?string $uploadUrl = null;

    protected string $view = 'filament-ckeditor-field::ckeditor';

    final public function __construct(string $name = 'ckeditor', ?string $uploadUrl = null)
    {
        $this->name($name);
        $this->uploadUrl($uploadUrl);
    }
 
    public static function make(string $name = 'ckeditor', ?string $uploadUrl = null): static
    {
        return app(static::class, [
            'name' => $name,
            'uploadUrl' => $uploadUrl,
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();
 
        $this->dehydrated(false);
    }

    public function uploadUrl(string | Closure $uploadUrl): self
    {
        $this->uploadUrl = $uploadUrl;

        return $this;
    }

    public function content(string | Closure | null $content): self
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

    public function getUploadUrl(): ?string
    {
        return $this->evaluate($this->uploadUrl);
    }
}