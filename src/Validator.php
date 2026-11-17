<?php
declare(strict_types=1);

namespace App;

final class Validator
{
    /** @var array<string, string> */
    private array $errors = [];

    public function __construct(private array $data)
    {
    }

    /** @param array<string, string> $fields поле => подпись */
    public function required(array $fields): self
    {
        foreach ($fields as $field => $label) {
            if (trim((string)($this->data[$field] ?? '')) === '') {
                $this->errors[$field] = "Поле «{$label}» обязательно.";
            }
        }

        return $this;
    }

    public function length(string $field, int $min, int $max, string $label): self
    {
        if ($this->failed($field)) {
            return $this;
        }

        $length = mb_strlen(trim((string)($this->data[$field] ?? '')));
        if ($length < $min || $length > $max) {
            $this->errors[$field] = "«{$label}»: от {$min} до {$max} символов.";
        }

        return $this;
    }

    public function integer(string $field, int $min, int $max, string $label): self
    {
        if ($this->failed($field)) {
            return $this;
        }

        $value = trim((string)($this->data[$field] ?? ''));
        if ($value === '') {
            return $this; // пустое значение допустимо, поле необязательное
        }

        if (preg_match('/^\d+$/', $value) !== 1) {
            $this->errors[$field] = "«{$label}» должно быть целым числом.";
            return $this;
        }

        if ((int)$value < $min || (int)$value > $max) {
            $this->errors[$field] = "«{$label}»: от {$min} до {$max}.";
        }

        return $this;
    }

    /** @param array<int, string> $allowed */
    public function in(string $field, array $allowed, string $label): self
    {
        $value = (string)($this->data[$field] ?? '');

        if (!in_array($value, $allowed, true)) {
            $this->errors[$field] = "Недопустимое значение «{$label}».";
        }

        return $this;
    }

    private function failed(string $field): bool
    {
        return isset($this->errors[$field]);
    }

    public function fails(): bool
    {
        return $this->errors !== [];
    }

    /** @return array<string, string> */
    public function errors(): array
    {
        return $this->errors;
    }
}
