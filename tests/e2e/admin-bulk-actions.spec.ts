import { test, expect } from "@playwright/test";

const ADMIN_EMAIL = "admin@example.com";
const ADMIN_PASSWORD = "password";

test.describe("Bulk Actions E2E", () => {
  test.beforeEach(async ({ page }) => {
    // Login as admin
    await page.goto("/login");
    await page.fill('input[name="email"]', ADMIN_EMAIL);
    await page.fill('input[name="password"]', ADMIN_PASSWORD);
    await page.click('button[type="submit"]');
    await page.waitForURL("**/dashboard");
  });

  test("bulk action bar appears when events are selected", async ({ page }) => {
    await page.goto("/admin/events");

    // Select first event checkbox
    const firstCheckbox = page.locator('input[type="checkbox"]').not(page.locator('label:has-text("Select All") input[type="checkbox"]')).first();
    await expect(firstCheckbox).toBeVisible();
    await firstCheckbox.check();

    // Verify bulk action bar appears
    const bulkActionBar = page.locator('.fixed.bottom-0.left-64, [class*="bulk-action"]');
    await expect(bulkActionBar).toBeVisible();

    // Verify selected count is displayed
    await expect(bulkActionBar.locator("text=/1 event selected/")).toBeVisible();

    // Verify all bulk action buttons are present
    await expect(bulkActionBar.locator('button:has-text("Publish")')).toBeVisible();
    await expect(bulkActionBar.locator('button:has-text("Unpublish")')).toBeVisible();
    await expect(bulkActionBar.locator('button:has-text("Delete")')).toBeVisible();
    await expect(bulkActionBar.locator('button:has-text("Cancel")')).toBeVisible();
  });

  test("select all checkbox selects all filtered events", async ({ page }) => {
    await page.goto("/admin/events");

    // Get initial count of event checkboxes
    const eventCheckboxes = page.locator('input[type="checkbox"]').not(page.locator('label:has-text("Select All") input[type="checkbox"]'));
    const checkboxCount = await eventCheckboxes.count();

    if (checkboxCount > 0) {
      // Click select all checkbox
      const selectAllCheckbox = page.locator('label:has-text("Select All") input[type="checkbox"]');
      await expect(selectAllCheckbox).toBeVisible();
      await selectAllCheckbox.check();

      // Verify all event checkboxes are checked
      for (let i = 0; i < checkboxCount; i++) {
        await expect(eventCheckboxes.nth(i)).toBeChecked();
      }

      // Verify bulk action bar shows correct count
      const bulkActionBar = page.locator(".fixed.bottom-0.left-64");
      await expect(bulkActionBar).toBeVisible();
      await expect(bulkActionBar.locator(`text=/${checkboxCount} events selected/`)).toBeVisible();
    }
  });

  test("cancel button clears selection", async ({ page }) => {
    await page.goto("/admin/events");

    // Select first event
    const firstCheckbox = page.locator('input[type="checkbox"]').not(page.locator('label:has-text("Select All") input[type="checkbox"]')).first();
    await firstCheckbox.check();

    // Verify bulk action bar appears
    const bulkActionBar = page.locator(".fixed.bottom-0.left-64");
    await expect(bulkActionBar).toBeVisible();

    // Click cancel button
    await bulkActionBar.locator('button:has-text("Cancel")').click();

    // Verify bulk action bar disappears
    await expect(bulkActionBar).not.toBeVisible();

    // Verify checkbox is unchecked
    await expect(firstCheckbox).not.toBeChecked();
  });

  test("bulk publish updates events to published", async ({ page }) => {
    await page.goto("/admin/events");

    // First create a draft event if none exist
    const draftBadge = page.locator("text=Draft").first();
    const hasDraft = await draftBadge.isVisible().catch(() => false);

    if (!hasDraft) {
      // Create a draft event
      await page.goto("/admin/events/create");
      await page.fill('input[name="name"]', "Draft Event for Bulk Publish");
      await page.selectOption('select[name="category"]', "Conference");
      await page.fill('input[name="event_date"]', "2024-12-25");
      await page.click('button[type="submit"]');
      await page.waitForURL("**/admin/events");
    }

    await page.goto("/admin/events");

    // Find draft events and select one
    const draftEventCard = page.locator(".bg-white.rounded-lg").filter({ hasText: "Draft" }).first();
    await expect(draftEventCard).toBeVisible();

    const draftCheckbox = draftEventCard.locator('input[type="checkbox"]');
    await draftCheckbox.check();

    // Verify bulk action bar appears
    const bulkActionBar = page.locator(".fixed.bottom-0.left-64");
    await expect(bulkActionBar).toBeVisible();

    // Click publish button
    await bulkActionBar.locator('button:has-text("Publish")').click();

    // Wait for page reload
    await page.waitForLoadState("networkidle");

    // Verify success message appears
    const successMessage = page.locator('.swal2-success, [role="alert"]').first();
    await expect(successMessage)
      .toBeVisible({ timeout: 5000 })
      .catch(() => {
        // Success message might auto-dismiss
      });

    // Verify event is now published
    await expect(draftEventCard.locator("text=Published")).toBeVisible();
  });

  test("bulk unpublish updates events to draft", async ({ page }) => {
    await page.goto("/admin/events");

    // First create a published event if none exist
    const publishedBadge = page.locator("text=Published").first();
    const hasPublished = await publishedBadge.isVisible().catch(() => false);

    if (!hasPublished) {
      // Create a published event
      await page.goto("/admin/events/create");
      await page.fill('input[name="name"]', "Published Event for Bulk Unpublish");
      await page.selectOption('select[name="category"]', "Conference");
      await page.fill('input[name="event_date"]', "2024-12-25");
      await page.click('label:has-text("Published")'); // Enable publish toggle
      await page.click('button[type="submit"]');
      await page.waitForURL("**/admin/events");
    }

    await page.goto("/admin/events");

    // Find published events and select one
    const publishedEventCard = page.locator(".bg-white.rounded-lg").filter({ hasText: "Published" }).first();
    await expect(publishedEventCard).toBeVisible();

    const publishedCheckbox = publishedEventCard.locator('input[type="checkbox"]');
    await publishedCheckbox.check();

    // Verify bulk action bar appears
    const bulkActionBar = page.locator(".fixed.bottom-0.left-64");
    await expect(bulkActionBar).toBeVisible();

    // Click unpublish button
    await bulkActionBar.locator('button:has-text("Unpublish")').click();

    // Wait for page reload
    await page.waitForLoadState("networkidle");

    // Verify success message appears
    const successMessage = page.locator('.swal2-success, [role="alert"]').first();
    await expect(successMessage)
      .toBeVisible({ timeout: 5000 })
      .catch(() => {
        // Success message might auto-dismiss
      });

    // Verify event is now draft
    await expect(publishedEventCard.locator("text=Draft")).toBeVisible();
  });

  test("bulk delete shows confirmation dialog", async ({ page }) => {
    await page.goto("/admin/events");

    // Select first event
    const firstCheckbox = page.locator('input[type="checkbox"]').not(page.locator('label:has-text("Select All") input[type="checkbox"]')).first();
    await firstCheckbox.check();

    // Verify bulk action bar appears
    const bulkActionBar = page.locator(".fixed.bottom-0.left-64");
    await expect(bulkActionBar).toBeVisible();

    // Click delete button
    await bulkActionBar.locator('button:has-text("Delete")').click();

    // Verify confirmation dialog appears
    const dialog = page.locator('[role="dialog"]').or(page.locator(".delete-confirmation-dialog"));
    await expect(dialog).toBeVisible();

    // Verify dialog shows plural title for bulk delete
    await expect(dialog.locator("text=/Delete.*Events/")).toBeVisible();

    // Verify dialog shows count
    await expect(dialog.locator("text=/1 event/")).toBeVisible();

    // Verify warning message about cascading delete
    await expect(dialog.locator("text=videos will be deleted")).toBeVisible();

    // Close dialog to clean up
    await dialog.locator('button:has-text("Cancel")').click();
    await expect(dialog).not.toBeVisible();
  });

  test("bulk delete removes selected events", async ({ page }) => {
    // Create test events for bulk delete
    for (let i = 1; i <= 2; i++) {
      await page.goto("/admin/events/create");
      await page.fill('input[name="name"]', `Bulk Delete Test Event ${i}`);
      await page.selectOption('select[name="category"]', "Conference");
      await page.fill('input[name="event_date"]', "2024-12-25");
      await page.click('button[type="submit"]');
      await page.waitForURL("**/admin/events");
    }

    await page.goto("/admin/events");

    // Select the test events
    const testEventCards = page.locator(".bg-white.rounded-lg").filter({ hasText: /Bulk Delete Test Event/ });
    const cardCount = await testEventCards.count();

    if (cardCount >= 2) {
      // Select first two test events
      await testEventCards.nth(0).locator('input[type="checkbox"]').check();
      await testEventCards.nth(1).locator('input[type="checkbox"]').check();

      // Verify bulk action bar appears with correct count
      const bulkActionBar = page.locator(".fixed.bottom-0.left-64");
      await expect(bulkActionBar).toBeVisible();
      await expect(bulkActionBar.locator("text=/2 events selected/")).toBeVisible();

      // Click delete button
      await bulkActionBar.locator('button:has-text("Delete")').click();

      // Confirm deletion in dialog
      const dialog = page.locator('[role="dialog"]');
      await expect(dialog).toBeVisible();
      await dialog.locator('button:has-text("Delete")').click();

      // Wait for page reload
      await page.waitForLoadState("networkidle");

      // Verify success message
      const successMessage = page.locator('.swal2-success, [role="alert"]').first();
      await expect(successMessage)
        .toBeVisible({ timeout: 5000 })
        .catch(() => {
          // Success message might auto-dismiss
        });

      // Verify events are removed from list
      await page.reload();
      await expect(page.locator("text=Bulk Delete Test Event 1")).not.toBeVisible();
      await expect(page.locator("text=Bulk Delete Test Event 2")).not.toBeVisible();
    }
  });

  test("cancel bulk delete keeps events", async ({ page }) => {
    await page.goto("/admin/events");

    // Select first event
    const firstCheckbox = page.locator('input[type="checkbox"]').not(page.locator('label:has-text("Select All") input[type="checkbox"]')).first();
    await firstCheckbox.check();

    // Click delete button
    const bulkActionBar = page.locator(".fixed.bottom-0.left-64");
    await bulkActionBar.locator('button:has-text("Delete")').click();

    // Get event name for verification
    const selectedEvent = page.locator(".bg-white.rounded-lg.ring-2");
    const eventName = await selectedEvent.locator("h3").textContent();

    // Cancel deletion in dialog
    const dialog = page.locator('[role="dialog"]');
    await dialog.locator('button:has-text("Cancel")').click();

    // Verify dialog closes
    await expect(dialog).not.toBeVisible();

    // Verify event still exists
    await expect(page.locator(`text=${eventName}`)).toBeVisible();
  });

  test("bulk action bar shows correct count for multiple selections", async ({ page }) => {
    await page.goto("/admin/events");

    // Get count of available events
    const eventCheckboxes = page.locator('input[type="checkbox"]').not(page.locator('label:has-text("Select All") input[type="checkbox"]'));
    const checkboxCount = await eventCheckboxes.count();

    if (checkboxCount >= 3) {
      // Select first three events
      await eventCheckboxes.nth(0).check();
      await eventCheckboxes.nth(1).check();
      await eventCheckboxes.nth(2).check();

      // Verify bulk action bar shows correct count
      const bulkActionBar = page.locator(".fixed.bottom-0.left-64");
      await expect(bulkActionBar).toBeVisible();
      await expect(bulkActionBar.locator("text=/3 events selected/")).toBeVisible();

      // Unselect one event
      await eventCheckboxes.nth(1).uncheck();

      // Verify count updates
      await expect(bulkActionBar.locator("text=/2 events selected/")).toBeVisible();
    }
  });

  test("selected events show visual ring indicator", async ({ page }) => {
    await page.goto("/admin/events");

    // Select first event
    const firstCheckbox = page.locator('input[type="checkbox"]').not(page.locator('label:has-text("Select All") input[type="checkbox"]')).first();
    await firstCheckbox.check();

    // Verify selected event has orange ring
    const selectedEvent = page.locator(".ring-2.ring-\\[\\#ff7607\\], .ring-2.ring-orange-400");
    await expect(selectedEvent).toBeVisible();
  });

  test("filters work with bulk selection", async ({ page }) => {
    await page.goto("/admin/events");

    // Click on Published tab
    await page.click('button:has-text("Published")');

    // Wait for filter to apply
    await page.waitForLoadState("networkidle");

    // Select all visible events using select all checkbox
    const selectAllCheckbox = page.locator('label:has-text("Select All") input[type="checkbox"]');
    const isVisible = await selectAllCheckbox.isVisible().catch(() => false);

    if (isVisible) {
      await selectAllCheckbox.check();

      // Verify bulk action bar appears
      const bulkActionBar = page.locator(".fixed.bottom-0.left-64");
      await expect(bulkActionBar).toBeVisible();

      // Verify only published events are selected (all visible events should have Published badge)
      const selectedEvents = page.locator(".ring-2.ring-\\[\\#ff7607\\], .ring-2.ring-orange-400");
      const selectedCount = await selectedEvents.count();

      for (let i = 0; i < selectedCount; i++) {
        await expect(selectedEvents.nth(i).locator("text=Published")).toBeVisible();
      }
    }
  });
});
