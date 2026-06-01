/**
 * Decode common HTML entities from OpenCart descriptions.
 */
export function decodeHtml(text: string | null | undefined): string {
  if (!text) {
    return "";
  }

  return text
    .replace(/&amp;/g, "&")
    .replace(/&lt;/g, "<")
    .replace(/&gt;/g, ">")
    .replace(/&quot;/g, '"')
    .replace(/&#039;/g, "'")
    .replace(/&#39;/g, "'");
}
