import { fetchApi } from "@/lib/api";
import type { ProductsMenuResponse, StoreInfo } from "@/types/api";

export function getStore(): Promise<StoreInfo> {
  return fetchApi<StoreInfo>("/store");
}

export function getProductsMenu(): Promise<ProductsMenuResponse> {
  return fetchApi<ProductsMenuResponse>("/navigation/products-menu", {
    revalidate: 600,
  });
}
