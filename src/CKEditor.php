<?php

namespace Kahusoftware\FilamentCkeditorField;

use Closure;
use Filament\Forms\Components\Field;

class CKEditor extends Field
{
    protected string | Closure $content = '';

    protected string $name = 'ckeditor';

    protected int $minLength = 0;

    protected string | Closure | null $uploadUrl = null;

    protected string $placeholder = 'Type or paste your content here...';

    protected string $view = 'filament-ckeditor-field::ckeditor';

    public static function make(?string $name = null): static
    {
        $field = app(static::class, [
            'name' => $name ?? 'ckeditor',
        ]);

        return $field;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->dehydrated(false);
    }

    public function uploadUrl(string | Closure | null $uploadUrl): self
    {
        $this->uploadUrl = $uploadUrl;

        return $this;
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

    public function placeholder(string $placeholder): self
    {
        $this->placeholder = $placeholder;

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

    public function getPlaceholder(): string
    {
        return $this->placeholder;
    }

    public function getUploadUrl(): ?string
    {
        return $this->evaluate($this->uploadUrl);
    }
}
