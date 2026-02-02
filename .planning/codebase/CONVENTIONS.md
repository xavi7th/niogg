# Coding Conventions

**Analysis Date:** 2026-02-02

## Naming Patterns

**Files:**
- Controllers: PascalCase (e.g., `AdminEventController.php`)
- Models: PascalCase (e.g., `Event.php`)
- Services: PascalCase (e.g., `VideoUploadService.php`)
- Requests: PascalCase (e.g., `EventFormRequest.php`)
- Factories: PascalCase with "Factory" suffix (e.g., `EventFactory.php`)
- Migration files: `YYYY_MM_DD_HHMMSS_create_table_name.php`

**Functions:**
- Public methods: camelCase with descriptive names (e.g., `bulkPublish()`, `initializeUpload()`)
- Private methods: camelCase prefixed with underscore (e.g., `_clearEventsCache()`)
- Static methods: camelCase (e.g., `generateUniqueSlug()`)
- Laravel lifecycle methods: snake_case (e.g., `boot()`, `creating()`)

**Variables:**
- Properties: camelCase (e.g., `$thumbnailService`)
- Local variables: camelCase (e.g., `$uploadId`, `$cacheKey`)
- Constants: UPPER_SNAKE_CASE (e.g., `MAX_FILE_SIZE`, `ALLOWED_MIME_TYPES`)
- Parameters: camelCase (e.g., `$request`, `$event`)

**Types:**
- Class names: PascalCase (e.g., `Event`, `User`)
- Interface names: PascalCase with "Interface" suffix (e.g., `EventInterface`)
- Trait names: PascalCase (e.g., `HasFactory`)
- Enum keys: TitleCase (e.g., `Active`, `Inactive`)

## Code Style

**Formatting:**
- Use Laravel Pint for code formatting (vendor/bin/pint)
- Use curly braces for all control structures, even single-line
- Indentation: 4 spaces
- Line length: Maximum 120 characters
- Trailing commas in multi-line arrays/parameters

**PHPDoc Blocks:**
- Required for all public methods and classes
- Include @param, @return, @throws annotations
- Use proper type hints
- Example:
```php
/**
 * Generate unique slug from name
 */
protected static function generateUniqueSlug(string $name): string
{
    // implementation
}
```

**Import Organization:**
1. PHP core classes (e.g., Exception)
2. Laravel framework classes (e.g., Illuminate\Support\Facades)
3. External package classes
4. Local application classes
5. Module classes (with full namespace)

**Return Types:**
- Always use explicit return type declarations
- Use appropriate PHP type hints
- For void methods: `: void`
- For nullable returns: `?Type`

## Error Handling

**Patterns:**
- Try-catch blocks for specific exceptions
- Throw InvalidArgumentException for invalid input
- Use Laravel ValidationException for form validation
- Log exceptions with context
- Return user-friendly error messages
- Include debug info only in development

**Example:**
```php
try {
    $event->update($request->validated());
    $this->clearEventsCache();
    return Redirect::route('admin.events.index')
        ->with('success', 'Event updated successfully.');
} catch (Exception $e) {
    Log::error('Failed to update event', [
        'event_id' => $event->id,
        'error' => $e->getMessage()
    ]);
    return Redirect::back()
        ->withInput()
        ->with('error', 'Failed to update event. Please try again.');
}
```

## Logging

**Framework:** Laravel's Log facade with channel support

**Patterns:**
- Use appropriate log levels: debug, info, warning, error, critical
- Include contextual data in log messages
- Structured logging for important operations
- Cache invalidation logging
- Error context with user ID, request ID

**Example:**
```php
Log::info('Video uploaded successfully', [
    'video_id' => $video->id,
    'event_id' => $event->id,
    'user_id' => auth()->id(),
    'file_size' => $fileSize,
    'upload_duration' => $duration,
]);
```

## Comments

**When to Comment:**
- Complex business logic
- Legacy code workarounds
- Important algorithm implementations
- TODO items with clear description

**JSDoc/TSDoc:**
- Used in Svelte components for props
- Include type information
- Document expected data types and formats
- Example: `/** @type {import('@root/types').Testimonial[]} */ export let testimonials;`

## Function Design

**Size:**
- Keep functions focused on single responsibility
- Maximum 50 lines per function
- Break complex logic into smaller private methods

**Parameters:**
- Use named parameters for optional values
- Limit to 7 parameters maximum
- Use Request objects for complex inputs
- Prefer dependency injection for services

**Return Values:**
- Return specific types instead of mixed
- Use collections for multiple items
- Return Response objects for controllers
- Use redirects with flash messages for user actions

## Module Design

**Exports:**
- Named exports in Svelte components
- Default exports only for main component
- Reuse existing components before creating new ones

**Barrel Files:**
- Not commonly used in this codebase
- Module-specific aliases used instead

**Service Layer:**
- Services handle business logic
- Controllers handle HTTP concerns only
- Services are injectable via constructor
- Use interfaces for service contracts

## Database Conventions

**Models:**
- Use Eloquent relationships properly
- Define fillable arrays
- Cast attributes in $casts array
- Use soft deletes when appropriate

**Migrations:**
- Use descriptive names
- Add indexes for frequently queried columns
- Use foreign key constraints
- Include timestamps by default

**Query Builder:**
- Use Eloquent ORM over raw queries
- Eager load relationships to prevent N+1
- Use scopes for reusable query logic

## Validation

**Form Requests:**
- Create dedicated FormRequest classes
- Include both validation rules and custom messages
- Authorize in FormRequest with middleware
- Use proper validation rules arrays

**Example:**
```php
public function rules(): array
{
    return [
        'name' => ['required', 'string', 'max:255'],
        'description' => ['nullable', 'string'],
        'event_date' => ['required', 'date'],
        'is_published' => ['boolean'],
    ];
}
```

---

*Convention analysis: 2026-02-02*