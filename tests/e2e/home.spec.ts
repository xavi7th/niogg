import { test, expect } from '@playwright/test';

test('home page loads correctly', async ({ page }) => {
  await page.goto('/');
  await expect(page).toHaveTitle(/Asuke Niogg/);
});

test('navigation links work', async ({ page }) => {
  await page.goto('/');
  await page.getByRole('link', { name: /Events/i }).click();
  await expect(page).toHaveURL(/events/);
});

test('contact form submission', async ({ page }) => {
  await page.goto('/contact');
  await page.fill('input[name="name"]', 'Test User');
  await page.fill('input[name="email"]', 'test@example.com');
  await page.fill('input[name="subject"]', 'Test Subject');
  await page.fill('textarea[name="message"]', 'Test message');
  await page.click('button[type="submit"]');
  await expect(page).toHaveURL('/contact');
});
