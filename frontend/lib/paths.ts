/**
 * Prefer API path/url for internal Next.js links.
 */
export function appPath(
  path: string | null | undefined,
  url: string | null | undefined,
  slug: string | null | undefined,
): string {
  if (path) {
    return path.startsWith("/") ? path : `/${path}`;
  }

  if (url) {
    try {
      const parsed = new URL(url);
      return parsed.pathname;
    } catch {
      return url.startsWith("/") ? url : `/${url}`;
    }
  }

  if (slug) {
    return slug.startsWith("/") ? slug : `/${slug}`;
  }

  return "#";
}
