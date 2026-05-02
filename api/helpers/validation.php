<?php
// ============================================================
// Validation Helper
// Web-Based Crop Insurance System
// ============================================================

/**
 * Validate an array of fields against rules.
 *
 * Rules supported:
 *   required, email, min:n, max:n, numeric, in:a,b,c, regex:/pattern/
 *
 * Usage:
 *   $errors = validate($_POST, [
 *       'email'    => 'required|email',
 *       'password' => 'required|min:8',
 *   ]);
 *
 * @return array  Empty array = valid; otherwise contains field => [messages]
 */
function validate(array $data, array $rules): array {
    $errors = [];

    foreach ($rules as $field => $ruleString) {
        $value      = $data[$field] ?? null;
        $fieldRules = explode('|', $ruleString);

        foreach ($fieldRules as $rule) {
            $param = null;
            if (str_contains($rule, ':')) {
                [$rule, $param] = explode(':', $rule, 2);
            }

            switch ($rule) {
                case 'required':
                    if ($value === null || trim((string)$value) === '') {
                        $errors[$field][] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
                    }
                    break;

                case 'email':
                    if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $errors[$field][] = ucfirst(str_replace('_', ' ', $field)) . ' must be a valid email address.';
                    }
                    break;

                case 'min':
                    if ($value !== null && strlen((string)$value) < (int)$param) {
                        $errors[$field][] = ucfirst(str_replace('_', ' ', $field)) . " must be at least $param characters.";
                    }
                    break;

                case 'max':
                    if ($value !== null && strlen((string)$value) > (int)$param) {
                        $errors[$field][] = ucfirst(str_replace('_', ' ', $field)) . " must not exceed $param characters.";
                    }
                    break;

                case 'numeric':
                    if ($value !== null && !is_numeric($value)) {
                        $errors[$field][] = ucfirst(str_replace('_', ' ', $field)) . ' must be a number.';
                    }
                    break;

                case 'in':
                    $allowed = explode(',', $param);
                    if ($value !== null && !in_array($value, $allowed, true)) {
                        $errors[$field][] = ucfirst(str_replace('_', ' ', $field)) . ' must be one of: ' . implode(', ', $allowed) . '.';
                    }
                    break;

                case 'regex':
                    if ($value !== null && !preg_match($param, (string)$value)) {
                        $errors[$field][] = ucfirst(str_replace('_', ' ', $field)) . ' format is invalid.';
                    }
                    break;

                case 'date':
                    if ($value && !strtotime($value)) {
                        $errors[$field][] = ucfirst(str_replace('_', ' ', $field)) . ' must be a valid date.';
                    }
                    break;
            }
        }
    }

    return $errors;
}

/**
 * Sanitize a string value
 */
function sanitize(string $value): string {
    return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
}

/**
 * Get and decode JSON request body
 */
function getJsonBody(): array {
    $raw = file_get_contents('php://input');
    return json_decode($raw, true) ?? [];
}

/**
 * Get pagination params from query string
 */
function getPagination(): array {
    $page    = max(1, (int)($_GET['page']     ?? 1));
    $perPage = min(100, max(1, (int)($_GET['per_page'] ?? 15)));
    $offset  = ($page - 1) * $perPage;
    return compact('page', 'perPage', 'offset');
}
