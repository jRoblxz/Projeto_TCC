import * as clsx from "clsx";
import { twMerge } from "tailwind-merge";

export function cn(...inputs: Parameters<typeof clsx.default>): string {
  return twMerge(clsx.default(...inputs));
}

