import Image from "next/image";
import Link from "next/link";
import { getBrands } from "@/lib/brands";
import { appPath } from "@/lib/paths";
import type { Metadata } from "next";

export const metadata: Metadata = {
  title: "Brands",
  description: "Shop by brand",
};

export default async function BrandsPage() {
  let brands: Awaited<ReturnType<typeof getBrands>>["data"] = [];

  try {
    const response = await getBrands(200);
    brands = response.data;
  } catch (error) {
    console.error("Failed to load brands", error);
  }

  return (
    <div className="container py-5">
      <h1 className="mb-4">Brands</h1>

      {brands.length === 0 ? (
        <p className="text-muted">No brands found.</p>
      ) : (
        <div className="row row-cols-2 row-cols-md-4 row-cols-lg-6 g-4">
          {brands.map((brand) => (
            <div key={brand.id} className="col">
              <Link
                href={appPath(null, brand.url, brand.slug)}
                className="card h-100 text-decoration-none text-dark brand-card"
              >
                <div className="card-body text-center">
                  {brand.image ? (
                    <Image
                      src={brand.image}
                      alt={brand.name}
                      width={120}
                      height={80}
                      className="img-fluid mb-2"
                      style={{ objectFit: "contain", maxHeight: "80px" }}
                      unoptimized
                    />
                  ) : (
                    <div
                      className="bg-light rounded mb-2 d-flex align-items-center justify-content-center"
                      style={{ height: "80px" }}
                    >
                      <span className="text-muted small">No logo</span>
                    </div>
                  )}
                  <h2 className="h6 mb-0">{brand.name}</h2>
                </div>
              </Link>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
