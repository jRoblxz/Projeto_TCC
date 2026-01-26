"use client";

import * as React from "react";
import * as SelectPrimitive from "@radix-ui/react-select";
import { CheckIcon, ChevronDownIcon, ChevronUpIcon } from "lucide-react";
import { cn } from "@/utils/utils";
import { useTheme } from "@/context/ThemeContext"; // <-- import do hook

// Tipagens permanecem iguais
type SelectProps = React.ComponentProps<typeof SelectPrimitive.Root>;
type SelectGroupProps = React.ComponentProps<typeof SelectPrimitive.Group>;
type SelectValueProps = React.ComponentProps<typeof SelectPrimitive.Value>;
type SelectTriggerProps = React.ComponentProps<typeof SelectPrimitive.Trigger> & { size?: "default" | "sm" };
type SelectContentProps = React.ComponentProps<typeof SelectPrimitive.Content> & { position?: "popper" | "item-aligned" };
type SelectLabelProps = React.ComponentProps<typeof SelectPrimitive.Label>;
type SelectItemProps = React.ComponentProps<typeof SelectPrimitive.Item>;
type SelectSeparatorProps = React.ComponentProps<typeof SelectPrimitive.Separator>;
type SelectScrollButtonProps = React.ComponentProps<typeof SelectPrimitive.ScrollUpButton> & React.ComponentProps<typeof SelectPrimitive.ScrollDownButton>;

// --- Componentes ---
export function Select(props: SelectProps) {
  return <SelectPrimitive.Root data-slot="select" {...props} />;
}

export function SelectGroup(props: SelectGroupProps) {
  return <SelectPrimitive.Group data-slot="select-group" {...props} />;
}

export function SelectValue(props: SelectValueProps) {
  return <SelectPrimitive.Value data-slot="select-value" {...props} />;
}

export function SelectTrigger({ className, size = "default", children, ...props }: SelectTriggerProps) {
  const { isDark } = useTheme(); // <-- pega estado do tema

  return (
    <SelectPrimitive.Trigger
      data-slot="select-trigger"
      data-size={size}
      className={cn(
        "flex w-fit items-center justify-between gap-2 rounded-md border px-3 py-2 text-sm whitespace-nowrap shadow-xs transition-[color,box-shadow] outline-none focus-visible:ring-[3px] disabled:cursor-not-allowed disabled:opacity-50 data-[size=default]:h-9 data-[size=sm]:h-8",
        isDark
          ? "bg-gray-800 border-gray-700 text-gray-100 focus-visible:ring-gray-400"
          : "bg-white border-gray-300 text-gray-900 focus-visible:ring-blue-500",
        className
      )}
      {...props}
    >
      {children}
      <SelectPrimitive.Icon asChild>
        <ChevronDownIcon className="size-4 opacity-50" />
      </SelectPrimitive.Icon>
    </SelectPrimitive.Trigger>
  );
}

export function SelectContent({ className, children, position = "popper", ...props }: SelectContentProps) {
  const { isDark } = useTheme();

  return (
    <SelectPrimitive.Portal>
      <SelectPrimitive.Content
        data-slot="select-content"
        className={cn(
          "relative z-50 max-h-(--radix-select-content-available-height) min-w-[8rem] origin-(--radix-select-content-transform-origin) overflow-x-hidden overflow-y-auto rounded-md border shadow-md",
          position === "popper" &&
            "data-[side=bottom]:translate-y-1 data-[side=left]:-translate-x-1 data-[side=right]:translate-x-1 data-[side=top]:-translate-y-1",
          isDark ? "bg-gray-800 text-gray-100 border-gray-700" : "bg-white text-gray-900 border-gray-300",
          className
        )}
        position={position}
        {...props}
      >
        <SelectScrollUpButton />
        <SelectPrimitive.Viewport className={cn("p-1", position === "popper" && "h-[var(--radix-select-trigger-height)] w-full min-w-[var(--radix-select-trigger-width)] scroll-my-1")}>
          {children}
        </SelectPrimitive.Viewport>
        <SelectScrollDownButton />
      </SelectPrimitive.Content>
    </SelectPrimitive.Portal>
  );
}

export function SelectItem({ className, children, ...props }: SelectItemProps) {
  const { isDark } = useTheme();

  return (
    <SelectPrimitive.Item
      data-slot="select-item"
      className={cn(
        "relative flex w-full cursor-default items-center gap-2 rounded-sm py-1.5 pr-8 pl-2 text-sm outline-hidden select-none",
        isDark ? "text-gray-100 focus:bg-gray-700" : "text-gray-900 focus:bg-gray-100",
        className
      )}
      {...props}
    >
      <span className="absolute right-2 flex size-3.5 items-center justify-center">
        <SelectPrimitive.ItemIndicator>
          <CheckIcon className="size-4" />
        </SelectPrimitive.ItemIndicator>
      </span>
      <SelectPrimitive.ItemText>{children}</SelectPrimitive.ItemText>
    </SelectPrimitive.Item>
  );
}

// O restante dos componentes permanece igual
export function SelectLabel({ className, ...props }: SelectLabelProps) {
  return <SelectPrimitive.Label data-slot="select-label" className={cn("text-muted-foreground px-2 py-1.5 text-xs", className)} {...props} />;
}

export function SelectSeparator({ className, ...props }: SelectSeparatorProps) {
  return <SelectPrimitive.Separator data-slot="select-separator" className={cn("bg-border pointer-events-none -mx-1 my-1 h-px", className)} {...props} />;
}

export function SelectScrollUpButton({ className, ...props }: SelectScrollButtonProps) {
  return (
    <SelectPrimitive.ScrollUpButton data-slot="select-scroll-up-button" className={cn("flex cursor-default items-center justify-center py-1", className)} {...props}>
      <ChevronUpIcon className="size-4" />
    </SelectPrimitive.ScrollUpButton>
  );
}

export function SelectScrollDownButton({ className, ...props }: SelectScrollButtonProps) {
  return (
    <SelectPrimitive.ScrollDownButton data-slot="select-scroll-down-button" className={cn("flex cursor-default items-center justify-center py-1", className)} {...props}>
      <ChevronDownIcon className="size-4" />
    </SelectPrimitive.ScrollDownButton>
  );
}
