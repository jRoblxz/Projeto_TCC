import React from "react";
import { FieldLabel } from "@/components/ui/field";
import { Input } from "@/components/ui/input";
import clsx from "clsx";

interface SelectModalInputProps {
  label: string;
  value: string;
  onOpen: () => void;
  error?: boolean;
  disabled?: boolean;
}

const SelectModalInput: React.FC<SelectModalInputProps> = ({
  label,
  value,
  onOpen,
  error,
  disabled,
}) => {
  return (
    <>
      <FieldLabel className="text-sm font-medium">{label}</FieldLabel>
      <div className="flex flex-col sm:flex-row gap-2 w-full">
        <Input
          disabled
          readOnly
          placeholder={`Selecione ${label}`}
          value={value}
          onClick={onOpen}
          className={clsx(
            "h-10 text-sm w-full rounded-md shadow-sm border transition-all",
            error ? "border-red-500" : "border-gray-600"
          )}
        />

        <button
          type="button"
          disabled={disabled}
          onClick={onOpen}
          className = {clsx(
                  "flex items-center gap-2 px-4 py-2 rounded-lg transition w-full sm:w-auto",
                  disabled
                    ? "bg-gray-400 cursor-Not-allowed text-white"
                    : "bg-blue-600 hover:bg-green-700 text-white font-semibold"
                )}
        >
          Selecionar
        </button>
      </div>
    </>
  );
};

export default SelectModalInput;