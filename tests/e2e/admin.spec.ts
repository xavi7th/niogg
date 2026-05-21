import { test, expect } from '@playwright/test';

test('admin login redirects to admin dashboard', async ({ page }) => {
  await page.goto('/login');
  await page.fill('input[name="email"]', 'admin@example.com');
  await page.fill('input[name="password"]', 'password');
  await page.click('button[type="submit"]');
  await expect(page).toHaveURL(/admin/);
});

test('admin can create event', async ({ page }) => {
  await page.goto('/login');
  await page.fill('input[name="email"]', 'admin@example.com');
  await page.fill('input[name="password"]', 'password');
  await page.click('button[type="submit"]');
  await page.goto('/admin/events/create');
  await expect(page).toHaveURL(/admin\/events\/create/);
});
