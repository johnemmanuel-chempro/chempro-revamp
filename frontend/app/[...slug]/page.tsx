type SlugPageProps = {
  params: Promise<{ slug: string[] }>;
};

/**
 * Catch-all route for OpenCart SEO slugs (e.g. /vitamins/product-slug).
 * Resolver + API wiring comes in a later step.
 */
export default async function SlugPage({ params }: SlugPageProps) {
  const { slug } = await params;
  const path = slug.join("/");

  return (
    <div className="container py-5">
      <h1 className="h2">Page: /{path}</h1>
      <p className="text-muted">
        This route will resolve the slug via the Laravel API and render product,
        category, or information content.
      </p>
    </div>
  );
}
