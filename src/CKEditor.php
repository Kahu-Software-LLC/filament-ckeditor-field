<?php

namespace Kahusoftware\FilamentCkeditorField;

use Closure;
use Filament\Forms\Components\Field;
use Kahusoftware\FilamentCkeditorField\Concerns\HasEditorOptions;

class CKEditor extends Field
{
    use HasEditorOptions;

    protected string | Closure $content = '';

    protected string $name = 'ckeditor';

    protected int $minLength = 0;

    protected string | Closure | null $uploadUrl = null;

    protected bool $uploadUrlExplicitlySet = false;

    protected string $placeholder = 'Type or paste your content here...';

    protected string | Closure | null $height = null;

    protected string | Closure | null $minHeight = null;

    protected string $view = 'filament-ckeditor-field::ckeditor';

    public static function make(?string $name = null): static
    {
        $field = app(static::class, [
            'name' => $name ?? 'ckeditor',
        ]);

        // Filament's own Field::make() calls this, and skipping it meant
        // CKEditor::configureUsing() callbacks were silently never applied.
        $field->configure();

        return $field;
    }

    public function uploadUrl(string | Closure | null $uploadUrl): self
    {
        $this->uploadUrl = $uploadUrl;
        $this->uploadUrlExplicitlySet = true;

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

    /**
     * Fix the editing area to the given CSS height, scrolling internally once
     * content outgrows it. Without it the editor grows with its content.
     */
    public function height(string | Closure | null $height): self
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Let the editing area start at the given CSS height while still growing
     * with its content.
     */
    public function minHeight(string | Closure | null $minHeight): self
    {
        $this->minHeight = $minHeight;

        return $this;
    }

    public function getHeight(): ?string
    {
        return $this->evaluate($this->height);
    }

    public function getMinHeight(): ?string
    {
        return $this->evaluate($this->minHeight);
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
        if ($this->uploadUrlExplicitlySet) {
            return $this->evaluate($this->uploadUrl);
        }

        // If not explicitly set, use config value as default
        return config('filament-ckeditor-field.upload_url');
    }
}
