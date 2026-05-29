import Image from "next/image";
import Link from "next/link";
import ProductsNavMenu from "@/components/layout/ProductsNavMenu";
import type { MenuCategory, StoreInfo } from "@/types/api";

type SiteHeaderProps = {
  store: StoreInfo;
  productCategories: MenuCategory[];
};

export default function SiteHeader({ store, productCategories }: SiteHeaderProps) {
  return (
    <header>
      <nav className="navbar navbar-expand-lg navbar-light bg-light border-bottom">
        <div className="container">
          <Link className="navbar-brand d-flex align-items-center gap-2" href="/">
            {store.logo ? (
              <Image
                src={store.logo}
                alt={store.name ?? "Store"}
                width={120}
                height={40}
                className="d-inline-block"
                style={{ width: "auto", height: "40px" }}
                unoptimized
              />
            ) : (
              <span>{store.name ?? "Chempro"}</span>
            )}
          </Link>

          <button
            className="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#mainNavbar"
            aria-controls="mainNavbar"
            aria-expanded="false"
            aria-label="Toggle navigation"
          >
            <span className="navbar-toggler-icon" />
          </button>

          <div className="collapse navbar-collapse" id="mainNavbar">
            <ul className="navbar-nav me-auto mb-2 mb-lg-0 align-items-lg-center">
              <li className="nav-item">
                <Link className="nav-link" href="/">
                  Home
                </Link>
              </li>

              <li className="nav-item dropdown">
                <button
                  className="nav-link dropdown-toggle"
                  type="button"
                  data-bs-toggle="dropdown"
                  data-bs-auto-close="outside"
                  aria-expanded="false"
                >
                  Products
                </button>
                <div className="dropdown-menu dropdown-menu-mega p-3">
                  <ProductsNavMenu categories={productCategories} />
                </div>
              </li>

              <li className="nav-item">
                <Link className="nav-link" href="/brands">
                  Brands
                </Link>
              </li>
            </ul>

            <ul className="navbar-nav mb-2 mb-lg-0">
              <li className="nav-item">
                <Link className="nav-link" href="/account">
                  Account
                </Link>
              </li>
            </ul>
          </div>
        </div>
      </nav>
    </header>
  );
}
