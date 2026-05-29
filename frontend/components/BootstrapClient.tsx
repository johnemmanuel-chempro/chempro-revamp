"use client";

import { useEffect } from "react";

/**
 * Loads Bootstrap JS (dropdowns, modals, collapse) on the client.
 */
export default function BootstrapClient() {
  useEffect(() => {
    void import("bootstrap");
  }, []);

  return null;
}
