"use client";

import Link from "next/link";
import { useEffect, useState } from "react";
import { decodeHtml } from "@/lib/html";
import { appPath } from "@/lib/paths";
import type { MenuCategory } from "@/types/api";

type ProductsNavMenuProps = {
  categories: MenuCategory[];
};

function ChevronRight() {
  return (
    <span className="mega-menu-chevron" aria-hidden>
      ›
    </span>
  );
}

function CategoryRow({
  category,
  isActive,
  hasChildren,
  onActivate,
}: {
  category: MenuCategory;
  isActive: boolean;
  hasChildren: boolean;
  onActivate: () => void;
}) {
  const href = appPath(category.path, category.url, category.slug);
  const label = decodeHtml(category.name);

  if (!hasChildren) {
    return (
      <Link
        href={href}
        className={`mega-menu-row${isActive ? " active" : ""}`}
        onMouseEnter={onActivate}
        onFocus={onActivate}
      >
        <span className="mega-menu-row-label">{label}</span>
      </Link>
    );
  }

  return (
    <button
      type="button"
      className={`mega-menu-row${isActive ? " active" : ""}`}
      onMouseEnter={onActivate}
      onFocus={onActivate}
      onClick={onActivate}
    >
      <span className="mega-menu-row-label">{label}</span>
      <ChevronRight />
    </button>
  );
}

function CategoryColumn({
  title,
  titleHref,
  categories,
  activeId,
  onActivate,
}: {
  title?: string;
  titleHref?: string;
  categories: MenuCategory[];
  activeId: number | null;
  onActivate: (id: number) => void;
}) {
  return (
    <div className="mega-menu-col">
      {title && (
        <div className="mega-menu-col-header">
          {titleHref ? (
            <Link href={titleHref} className="mega-menu-col-title">
              {title}
            </Link>
          ) : (
            <span className="mega-menu-col-title">{title}</span>
          )}
        </div>
      )}
      <ul className="list-unstyled mb-0 mega-menu-col-list">
        {categories.map((category) => {
          const hasChildren = category.children.length > 0;

          return (
            <li key={category.id}>
              <CategoryRow
                category={category}
                isActive={category.id === activeId}
                hasChildren={hasChildren}
                onActivate={() => onActivate(category.id)}
              />
            </li>
          );
        })}
      </ul>
    </div>
  );
}

export default function ProductsNavMenu({ categories }: ProductsNavMenuProps) {
  const [activeL1Id, setActiveL1Id] = useState<number>(categories[0]?.id ?? 0);
  const [activeL2Id, setActiveL2Id] = useState<number | null>(null);

  const activeL1 =
    categories.find((category) => category.id === activeL1Id) ?? categories[0];
  const l2Items = activeL1?.children ?? [];
  const activeL2 =
    l2Items.find((category) => category.id === activeL2Id) ??
    (l2Items.length > 0 ? l2Items[0] : null);
  const l3Items = activeL2?.children ?? [];

  useEffect(() => {
    if (!activeL1) {
      return;
    }

    if (l2Items.length === 0) {
      setActiveL2Id(null);
      return;
    }

    const stillValid = l2Items.some((category) => category.id === activeL2Id);
    if (!stillValid) {
      setActiveL2Id(l2Items[0].id);
    }
  }, [activeL1Id, activeL1, l2Items, activeL2Id]);

  if (categories.length === 0) {
    return <p className="text-muted mb-0 px-3 py-2">No categories available.</p>;
  }

  const showL2 = l2Items.length > 0;
  const showL3 = l3Items.length > 0;

  return (
    <div
      className={`products-mega-menu d-flex${showL2 ? " has-l2" : ""}${showL3 ? " has-l3" : ""}`}
    >
      <CategoryColumn
        categories={categories}
        activeId={activeL1?.id ?? null}
        onActivate={setActiveL1Id}
      />

      {showL2 && activeL1 && (
        <CategoryColumn
          title={decodeHtml(activeL1.name) ?? ""}
          titleHref={appPath(activeL1.path, activeL1.url, activeL1.slug)}
          categories={l2Items}
          activeId={activeL2?.id ?? null}
          onActivate={setActiveL2Id}
        />
      )}

      {showL3 && activeL2 && (
        <div className="mega-menu-col">
          <div className="mega-menu-col-header">
            <Link
              href={appPath(activeL2.path, activeL2.url, activeL2.slug)}
              className="mega-menu-col-title"
            >
              {decodeHtml(activeL2.name)}
            </Link>
          </div>
          <ul className="list-unstyled mb-0 mega-menu-col-list">
            {l3Items.map((category) => (
              <li key={category.id}>
                <Link
                  href={appPath(category.path, category.url, category.slug)}
                  className="mega-menu-row"
                >
                  <span className="mega-menu-row-label">{decodeHtml(category.name)}</span>
                </Link>
              </li>
            ))}
          </ul>
        </div>
      )}
    </div>
  );
}
