import type { Metadata } from "next";
import BootstrapClient from "@/components/BootstrapClient";
import SiteFooter from "@/components/layout/SiteFooter";
import SiteHeader from "@/components/layout/SiteHeader";
import { getProductsMenu, getStore } from "@/lib/store";
import type { MenuCategory, StoreInfo } from "@/types/api";
import "bootstrap/dist/css/bootstrap.min.css";
import "./globals.css";

export async function generateMetadata(): Promise<Metadata> {
  try {
    const store = await getStore();
    return {
      title: store.meta.title ?? store.name ?? "Chempro",
      description: store.meta.description ?? undefined,
      keywords: store.meta.keywords ?? undefined,
    };
  } catch {
    return {
      title: "Chempro",
      description: "Chempro storefront",
    };
  }
}

export default async function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  let store: StoreInfo = {
    name: "Chempro",
    logo: null,
    email: null,
    telephone: null,
    meta: { title: null, description: null, keywords: null },
  };
  let productCategories: MenuCategory[] = [];

  try {
    const [storeData, menuData] = await Promise.all([getStore(), getProductsMenu()]);
    store = storeData;
    productCategories = menuData.categories;
  } catch (error) {
    console.error("Failed to load header data from API", error);
  }

  return (
    <html lang="en">
      <body>
        <BootstrapClient />
        <SiteHeader store={store} productCategories={productCategories} />
        <main>{children}</main>
        <SiteFooter />
      </body>
    </html>
  );
}
