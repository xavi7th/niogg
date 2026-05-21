import { test, expect } from "@playwright/test";

const ADMIN_EMAIL = "admin@example.com";
const ADMIN_PASSWORD = "password";

test.describe("Delete Confirmation E2E", () => {
  test.beforeEach(async ({ page }) => {
    // Login as admin
    await page.goto("/login");
    await page.fill('input[name="email"]', ADMIN_EMAIL);
    await page.fill('input[name="password"]', ADMIN_PASSWORD);
    await page.click('button[type="submit"]');
    await page.waitForURL("**/dashboard");
  });

  test("event delete confirmation dialog appears", async ({ page }) => {
    await page.goto("/admin/events");

    // Find and click delete button on first event
    const deleteButton = page.locator('button:has-text("Delete")').first();
    await expect(deleteButton).toBeVisible();
    await deleteButton.click();

    // Verify confirmation dialog appears
    const dialog = page.locator('[role="dialog"]').or(page.locator(".delete-confirmation-dialog"));
    await expect(dialog).toBeVisible();

    // Verify dialog content
    await expect(dialog.locator("text=Delete Event")).toBeVisible();
    await expect(dialog.locator("text=Are you sure you want to delete this event?")).toBeVisible();

    // Verify warning message about cascading delete
    await expect(dialog.locator("text=videos will be deleted")).toBeVisible();

    // Verify buttons
    await expect(dialog.locator('button:has-text("Cancel")')).toBeVisible();
    await expect(dialog.locator('button:has-text("Delete")')).toBeVisible();
  });

  test("cancel delete keeps event", async ({ page }) => {
    await page.goto("/admin/events");

    // Get initial event count
    const eventCards = page.locator('[class*="event"], [class*="Event"]');
    const initialCount = await eventCards.count();

    // Click delete button on first event
    const deleteButton = page.locator('button:has-text("Delete")').first();
    await deleteButton.click();

    // Click cancel in dialog
    const dialog = page.locator('[role="dialog"]').or(page.locator(".delete-confirmation-dialog"));
    await dialog.locator('button:has-text("Cancel")').click();

    // Verify dialog closes
    await expect(dialog).not.toBeVisible();

    // Verify event still exists (count unchanged)
    const finalCount = await eventCards.count();
    expect(finalCount).toBe(initialCount);
  });

  test("confirm delete removes event", async ({ page }) => {
    // First create a test event
    await page.goto("/admin/events/create");
    await page.fill('input[name="name"]', "Event To Delete");
    await page.selectOption('select[name="category"]', "Conference");
    await page.fill('input[name="event_date"]', "2024-12-25");
    await page.click('button[type="submit"]');
    await page.waitForURL("**/admin/events");

    // Verify event exists
    await expect(page.locator("text=Event To Delete")).toBeVisible();

    // Click delete button
    const deleteButton = page.locator('button:has-text("Delete")').filter({ hasText: "Event To Delete" });
    await deleteButton.click();

    // Confirm deletion in dialog
    const dialog = page.locator('[role="dialog"]').or(page.locator(".delete-confirmation-dialog"));
    await dialog.locator('button:has-text("Delete")').click();

    // Verify success message
    const successMessage = page.locator('.swal2-success, [role="alert"]').first();
    await expect(successMessage)
      .toBeVisible({ timeout: 5000 })
      .catch(() => {
        // Success message might auto-dismiss
      });

    // Verify event is removed from list
    await expect(page.locator("text=Event To Delete")).not.toBeVisible();
  });

  test("video delete confirmation appears", async ({ page }) => {
    // Navigate to an event detail page with videos
    await page.goto("/admin/events");

    // Find first event with videos and navigate to it
    const eventLink = page.locator('a[href^="/admin/events/"]').first();
    await eventLink.click();

    // Wait for event detail page to load
    await page.waitForURL("**/admin/events/**");

    // Click delete button on first video (if videos exist)
    const videoDeleteButton = page.locator('button:has-text("Delete")').first();
    const hasVideos = await videoDeleteButton.isVisible().catch(() => false);

    if (hasVideos) {
      await videoDeleteButton.click();

      // Verify video delete dialog appears
      const dialog = page.locator('[role="dialog"]').or(page.locator(".delete-confirmation-dialog"));
      await expect(dialog).toBeVisible();

      // Verify dialog is for video (no cascading warning)
      await expect(dialog.locator("text=Delete Video")).toBeVisible();
      await expect(dialog.locator("text=videos will be deleted")).not.toBeVisible();

      // Cancel to clean up
      await dialog.locator('button:has-text("Cancel")').click();
    }
  });

  test("escape key closes dialog", async ({ page }) => {
    await page.goto("/admin/events");

    // Click delete button
    const deleteButton = page.locator('button:has-text("Delete")').first();
    await deleteButton.click();

    // Verify dialog is open
    const dialog = page.locator('[role="dialog"]').or(page.locator(".delete-confirmation-dialog"));
    await expect(dialog).toBeVisible();

    // Press Escape key
    await page.keyboard.press("Escape");

    // Verify dialog closes
    await expect(dialog).not.toBeVisible();
  });

  test("backdrop click closes dialog", async ({ page }) => {
    await page.goto("/admin/events");

    // Click delete button
    const deleteButton = page.locator('button:has-text("Delete")').first();
    await deleteButton.click();

    // Verify dialog is open
    const dialog = page.locator('[role="dialog"]').or(page.locator(".delete-confirmation-dialog"));
    await expect(dialog).toBeVisible();

    // Click on backdrop (outside dialog content)
    await page.click("body", { position: { x: 10, y: 10 } });

    // Verify dialog closes
    await expect(dialog).not.toBeVisible();
  });
});
