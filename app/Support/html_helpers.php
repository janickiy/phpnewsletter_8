<?php

use Illuminate\Contracts\Support\Arrayable;

if (! function_exists('form_open')) {
    function form_open(array $options = []): string
    {
        $method = strtoupper((string) ($options['method'] ?? 'POST'));
        $action = $options['url'] ?? null;

        if (array_key_exists('route', $options)) {
            $route = $options['route'];
            $action = is_array($route)
                ? route(array_shift($route), count($route) === 1 && is_array($route[0]) ? $route[0] : $route)
                : route($route);
        }

        $files = (bool) ($options['files'] ?? false);
        unset($options['method'], $options['url'], $options['route'], $options['files']);

        $formMethod = in_array($method, ['GET', 'POST'], true) ? $method : 'POST';
        $options = array_merge($options, ['method' => $formMethod]);

        if ($action !== null) {
            $options['action'] = $action;
        }

        if ($files) {
            $options['enctype'] = 'multipart/form-data';
        }

        $html = '<form'.form_attributes($options).'>';

        if ($method !== 'GET') {
            $html .= csrf_field();
        }

        if (! in_array($method, ['GET', 'POST'], true)) {
            $html .= method_field($method);
        }

        return $html;
    }
}

if (! function_exists('form_close')) {
    function form_close(): string
    {
        return '</form>';
    }
}

if (! function_exists('form_label')) {
    function form_label(string $name, ?string $value = null, array $attributes = []): string
    {
        $attributes['for'] ??= form_id($name);

        return '<label'.form_attributes($attributes).'>'.e($value ?? $name).'</label>';
    }
}

if (! function_exists('form_text')) {
    function form_text(string $name, mixed $value = null, array $attributes = []): string
    {
        return form_input('text', $name, form_value($name, $value), $attributes);
    }
}

if (! function_exists('form_password')) {
    function form_password(string $name, array $attributes = []): string
    {
        return form_input('password', $name, null, $attributes);
    }
}

if (! function_exists('form_hidden')) {
    function form_hidden(string $name, mixed $value = null, array $attributes = []): string
    {
        return form_input('hidden', $name, $value, $attributes);
    }
}

if (! function_exists('form_textarea')) {
    function form_textarea(string $name, mixed $value = null, array $attributes = []): string
    {
        $attributes['name'] = $name;
        $attributes['id'] ??= form_id($name);

        return '<textarea'.form_attributes($attributes).'>'.e(form_value($name, $value)).'</textarea>';
    }
}

if (! function_exists('form_file')) {
    function form_file(string $name, array $attributes = []): string
    {
        return form_input('file', $name, null, $attributes);
    }
}

if (! function_exists('form_select')) {
    function form_select(string $name, mixed $list = [], mixed $selected = null, array $attributes = []): string
    {
        $placeholder = $attributes['placeholder'] ?? null;
        unset($attributes['placeholder']);

        $selected = form_value($name, $selected);
        $attributes['name'] = $name;
        $attributes['id'] ??= form_id($name);

        $html = '<select'.form_attributes($attributes).'>';

        if ($placeholder !== null) {
            $html .= '<option value="">'.e($placeholder).'</option>';
        }

        foreach (form_options($list) as $value => $label) {
            if (is_array($label)) {
                $html .= '<optgroup label="'.e($value).'">';
                foreach ($label as $nestedValue => $nestedLabel) {
                    $html .= form_option($nestedValue, $nestedLabel, $selected);
                }
                $html .= '</optgroup>';
                continue;
            }

            $html .= form_option($value, $label, $selected);
        }

        return $html.'</select>';
    }
}

if (! function_exists('form_radio')) {
    function form_radio(string $name, mixed $value = 1, mixed $checked = null, array $attributes = []): string
    {
        return form_checkable('radio', $name, $value, $checked, $attributes);
    }
}

if (! function_exists('form_checkbox')) {
    function form_checkbox(string $name, mixed $value = 1, mixed $checked = null, array $attributes = []): string
    {
        return form_checkable('checkbox', $name, $value, $checked, $attributes);
    }
}

if (! function_exists('form_submit')) {
    function form_submit(string $value, array $attributes = []): string
    {
        $attributes['type'] = 'submit';

        return '<button'.form_attributes($attributes).'>'.e($value).'</button>';
    }
}

if (! function_exists('form_button')) {
    function form_button(string $value, array $attributes = []): string
    {
        $attributes['type'] ??= 'button';

        return '<button'.form_attributes($attributes).'>'.e($value).'</button>';
    }
}

if (! function_exists('form_input')) {
    function form_input(string $type, string $name, mixed $value = null, array $attributes = []): string
    {
        $attributes['type'] = $type;
        $attributes['name'] = $name;
        $attributes['id'] ??= form_id($name);

        if ($value !== null && $type !== 'file') {
            $attributes['value'] = $value;
        }

        return '<input'.form_attributes($attributes).'>';
    }
}

if (! function_exists('form_checkable')) {
    function form_checkable(string $type, string $name, mixed $value = 1, mixed $checked = null, array $attributes = []): string
    {
        if (is_array($checked) && $attributes === []) {
            $attributes = $checked;
            $checked = false;
        }

        $oldValue = form_old_value($name);
        if ($oldValue !== null) {
            $checked = is_array($oldValue)
                ? in_array((string) $value, array_map('strval', $oldValue), true)
                : (string) $oldValue === (string) $value;
        }

        $attributes['type'] = $type;
        $attributes['name'] = $name;
        $attributes['value'] = $value;
        $attributes['id'] ??= form_checkable_id($name, $value, $type);

        if ($checked) {
            $attributes['checked'] = true;
        }

        return '<input'.form_attributes($attributes).'>';
    }
}

if (! function_exists('form_option')) {
    function form_option(mixed $value, mixed $label, mixed $selected): string
    {
        $attributes = ['value' => $value];
        $selectedValues = is_array($selected) ? $selected : [$selected];

        if (in_array((string) $value, array_map('strval', $selectedValues), true)) {
            $attributes['selected'] = true;
        }

        return '<option'.form_attributes($attributes).'>'.e($label).'</option>';
    }
}

if (! function_exists('form_options')) {
    function form_options(mixed $options): array
    {
        if ($options instanceof Arrayable) {
            return $options->toArray();
        }

        if ($options instanceof Traversable) {
            return iterator_to_array($options);
        }

        return is_array($options) ? $options : [];
    }
}

if (! function_exists('form_value')) {
    function form_value(string $name, mixed $value = null): mixed
    {
        $oldValue = form_old_value($name);

        if ($oldValue !== null && ! is_array($oldValue)) {
            return $oldValue;
        }

        return $value;
    }
}

if (! function_exists('form_old_value')) {
    function form_old_value(string $name): mixed
    {
        $key = form_old_key($name);

        return session()->hasOldInput($key) ? old($key) : null;
    }
}

if (! function_exists('form_old_key')) {
    function form_old_key(string $name): string
    {
        return preg_replace('/\[\]$/', '', $name) ?? $name;
    }
}

if (! function_exists('form_id')) {
    function form_id(string $name): string
    {
        $name = form_old_key($name);

        return trim(preg_replace('/[^A-Za-z0-9_\-:.]+/', '_', $name) ?? $name, '_');
    }
}

if (! function_exists('form_checkable_id')) {
    function form_checkable_id(string $name, mixed $value, string $type): string
    {
        if ($type === 'radio' || str_contains($name, '[]')) {
            return form_id($name).'_'.form_id((string) $value);
        }

        return form_id($name);
    }
}

if (! function_exists('form_attributes')) {
    function form_attributes(array $attributes): string
    {
        $html = '';
        $booleanAttributes = [
            'allowfullscreen', 'async', 'autofocus', 'checked', 'controls', 'defer',
            'disabled', 'formnovalidate', 'hidden', 'loop', 'multiple', 'muted',
            'novalidate', 'open', 'readonly', 'required', 'selected',
        ];

        foreach ($attributes as $key => $value) {
            if ($value === null || $value === false) {
                continue;
            }

            if (is_array($value)) {
                $value = implode(' ', array_filter($value));
            }

            if ($value === true || ($value === '' && in_array($key, $booleanAttributes, true))) {
                $html .= ' '.e($key);
                continue;
            }

            $html .= ' '.e($key).'="'.e((string) $value).'"';
        }

        return $html;
    }
}
