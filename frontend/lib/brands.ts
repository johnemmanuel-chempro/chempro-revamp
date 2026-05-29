import { fetchApi } from "@/lib/api";
import type { Brand, PaginatedResponse } from "@/types/api";

export function getBrands(perPage = 100): Promise<PaginatedResponse<Brand>> {
  return fetchApi<PaginatedResponse<Brand>>(
    `/brands?per_page=${perPage}`,
    { revalidate: 300 },
  );
}
