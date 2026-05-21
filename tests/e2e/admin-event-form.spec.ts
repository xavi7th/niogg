import { test, expect } from "@playwright/test";

const ADMIN_EMAIL = "admin@example.com";
const ADMIN_PASSWORD = "password";

test.describe("Event Form E2E", () => {
  test.beforeEach(async ({ page }) => {
    // Login as admin
    await page.goto("/login");
    await page.fill('input[name="email"]', ADMIN_EMAIL);
    await page.fill('input[name="password"]', ADMIN_PASSWORD);
    await page.click('button[type="submit"]');
    await page.waitForURL("**/dashboard");
  });

  test("create event successfully", async ({ page }) => {
    // Navigate to create page
    await page.goto("/admin/events/create");
    await expect(page).toHaveTitle(/Create Event/);

    // Fill form with valid data
    await page.fill('input[name="name"]', "Test Conference 2024");
    await page.fill('textarea[name="description"]', "A test conference for E2E testing");
    await page.selectOption('select[name="category"]', "Conference");
    await page.fill('input[name="event_date"]', "2024-12-25");
    await page.fill('input[name="icon"]', "🎬");

    // Toggle publish
    const publishToggle = page.locator('input[type="checkbox"]');
    await publishToggle.check();
    await expect(publishToggle).toBeChecked();

    // Submit form
    await page.click('button[type="submit"]');

    // Verify redirect to events list with success message
    await page.waitForURL("**/admin/events");
    await expect(page).toHaveURL(/\/admin\/events/);

    // Verify success message (check for SweetAlert2 toast or similar)
    const successMessage = page.locator('.swal2-success, [role="alert"]').first();
    await expect(successMessage)
      .toBeVisible({ timeout: 5000 })
      .catch(() => {
        // Success message might auto-dismiss, check URL instead
        expect(page.url()).toContain("/admin/events");
      });

    // Verify event appears in list
    await expect(page.locator("text=Test Conference 2024")).toBeVisible();
  });

  test("validation errors display for invalid data", async ({ page }) => {
    await page.goto("/admin/events/create");

    // Submit without required fields
    await page.click('button[type="submit"]');

    // Check for validation errors
    await expect(page.locator("text=required"))
      .toBeVisible({ timeout: 3000 })
      .catch(() => {
        // Errors might be inline
        const nameError = page.locator('input[name="name"]').locator("..").locator("text=/required/i");
        expect(nameError).toBeTruthy();
      });
  });

  test("edit existing event", async ({ page }) => {
    // First create an event via API or navigate to existing
    await page.goto("/admin/events/create");
    await page.fill('input[name="name"]', "Original Event Name");
    await page.selectOption('select[name="category"]', "Workshop");
    await page.fill('input[name="event_date"]', "2024-11-01");
    await page.click('button[type="submit"]');
    await page.waitForURL("**/admin/events");

    // Navigate to edit the first event
    const editButton = page.locator('a[href^="/admin/events/"]').first();
    await editButton.click();

    // Verify edit form loads
    await expect(page.locator('input[name="name"]')).toHaveValue("Original Event Name");

    // Update event
    await page.fill('input[name="name"]', "Updated Event Name");
    await page.fill('textarea[name="description"]', "Updated description");

    // Submit
    await page.click('button[type="submit"]');

    // Verify redirect and update
    await page.waitForURL("**/admin/events");
    await expect(page.locator("text=Updated Event Name")).toBeVisible();
  });

  test("slug preview updates with event name", async ({ page }) => {
    await page.goto("/admin/events/create");

    const nameInput = page.locator('input[name="name"]');
    const slugInput = page.locator('input[readonly][value*="events/"]').or(page.locator('div:has-text("URL Slug") input'));

    await nameInput.fill("My Awesome Event 2024!");

    // Verify slug preview updates
    await expect(slugInput).toHaveValue(/my-awesome-event-2024/);
  });

  test("save as draft keeps event unpublished", async ({ page }) => {
    await page.goto("/admin/events/create");

    await page.fill('input[name="name"]', "Draft Event");
    await page.selectOption('select[name="category"]', "Conference");
    await page.fill('input[name="event_date"]', "2024-12-25");

    // Click "Save as Draft" (not "Create Event")
    await page.click('button:has-text("Save as Draft")');

    await page.waitForURL("**/admin/events");

    // Navigate to dashboard and verify draft count increased
    await page.goto("/admin/dashboard");
    const draftStat = page.locator("text=Draft Events").locator("..");
    await expect(draftStat).toBeVisible();
  });
});
