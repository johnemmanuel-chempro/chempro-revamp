export type StoreInfo = {
  name: string;
  logo: string | null;
  email: string | null;
  telephone: string | null;
  meta: {
    title: string | null;
    description: string | null;
    keywords: string | null;
  };
};

export type MenuProduct = {
  id: number;
  name: string | null;
  slug: string | null;
  path: string | null;
  url: string | null;
  price: number;
};

export type MenuCategory = {
  id: number;
  name: string | null;
  slug: string | null;
  path: string | null;
  url: string | null;
  children: MenuCategory[];
  products: MenuProduct[];
};

export type ProductsMenuResponse = {
  categories: MenuCategory[];
};

export type Brand = {
  id: number;
  name: string;
  image: string | null;
  sort_order: number;
  slug: string | null;
  url: string | null;
};

export type PaginatedResponse<T> = {
  data: T[];
  links: {
    first: string | null;
    last: string | null;
    prev: string | null;
    next: string | null;
  };
  meta: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
};
