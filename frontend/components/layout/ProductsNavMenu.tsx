import Link from "next/link";
import { appPath } from "@/lib/paths";
import type { MenuCategory, MenuProduct } from "@/types/api";

type ProductsNavMenuProps = {
  categories: MenuCategory[];
};

function ProductLinks({ products }: { products: MenuProduct[] }) {
  if (products.length === 0) {
    return null;
  }

  return (
    <ul className="list-unstyled mb-0 ps-2 border-start">
      {products.map((product) => (
        <li key={product.id}>
          <Link
            className="dropdown-item py-1 small"
            href={appPath(product.path, product.url, product.slug)}
          >
            {product.name}
            {product.price > 0 && (
              <span className="text-muted ms-1">${product.price.toFixed(2)}</span>
            )}
          </Link>
        </li>
      ))}
    </ul>
  );
}

function Level3List({ categories }: { categories: MenuCategory[] }) {
  return (
    <ul className="list-unstyled mb-2 small">
      {categories.map((category) => (
        <li key={category.id} className="mb-2">
          <Link
            className="fw-semibold text-decoration-none"
            href={appPath(category.path, category.url, category.slug)}
          >
            {category.name}
          </Link>
          <ProductLinks products={category.products} />
        </li>
      ))}
    </ul>
  );
}

function Level2List({ categories }: { categories: MenuCategory[] }) {
  return (
    <ul className="list-unstyled mb-0">
      {categories.map((category) => (
        <li key={category.id} className="mb-3">
          <Link
            className="fw-semibold text-decoration-none"
            href={appPath(category.path, category.url, category.slug)}
          >
            {category.name}
          </Link>
          {category.children.length > 0 ? (
            <Level3List categories={category.children} />
          ) : (
            <ProductLinks products={category.products} />
          )}
        </li>
      ))}
    </ul>
  );
}

export default function ProductsNavMenu({ categories }: ProductsNavMenuProps) {
  if (categories.length === 0) {
    return <p className="text-muted mb-0">No categories available.</p>;
  }

  return (
    <div className="row g-4">
      {categories.map((category) => (
        <div key={category.id} className="col-12 col-md-6 col-lg-4 col-xl-3">
          <h6 className="text-uppercase fw-bold mb-2">
            <Link
              className="text-decoration-none"
              href={appPath(category.path, category.url, category.slug)}
            >
              {category.name}
            </Link>
          </h6>
          {category.children.length > 0 ? (
            <Level2List categories={category.children} />
          ) : (
            <ProductLinks products={category.products} />
          )}
        </div>
      ))}
    </div>
  );
}
