const API_URL = process.env.NEXT_PUBLIC_API_URL ?? "http://127.0.0.1:8000/api";

export class ApiError extends Error {
  constructor(
    message: string,
    public status: number,
  ) {
    super(message);
    this.name = "ApiError";
  }
}

export async function fetchApi<T>(
  path: string,
  init?: RequestInit & { revalidate?: number },
): Promise<T> {
  const { revalidate = 300, ...requestInit } = init ?? {};

  const response = await fetch(`${API_URL}${path}`, {
    ...requestInit,
    headers: {
      Accept: "application/json",
      ...requestInit.headers,
    },
    next: { revalidate },
  });

  if (!response.ok) {
    throw new ApiError(`API ${response.status}: ${path}`, response.status);
  }

  return response.json() as Promise<T>;
}
