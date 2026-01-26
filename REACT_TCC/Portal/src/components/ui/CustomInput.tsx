import React from "react";
import { Input } from "@/components/ui/input";
import { FieldLabel } from "@/components/ui/field";
import clsx from "clsx";

interface CustomInputProps {
  label?: string;
  // "any" permite string (texto), number ou File (upload)
  value?: string | number | readonly string[] | undefined; 
  // Ajustamos o onChange para ser mais flexível
  onChange: (value: any) => void; 
  placeholder?: string;
  maxLength?: number;
  // Estendemos os tipos para incluir 'select'
  type?: "text" | "number" | "email" | "password" | "date" | "file" | "select";
  mask?: "cpf" | "cnpj" | "telefone" | "cep" | "data" | "numero" | "moeda";
  error?: boolean;
  disabled?: boolean;
  readOnly?: boolean;
  className?: string;
  // Props extras para select e file
  children?: React.ReactNode; 
  accept?: string;
}

const applyMask = (value: string, mask?: string) => {
  let v = value.replace(/\D/g, "");

  switch (mask) {
    case "cpf":
      return v
        .replace(/\D/g, "")
        .replace(/^(\d{3})(\d)/, "$1.$2")
        .replace(/^(\d{3})\.(\d{3})(\d)/, "$1.$2.$3")
        .replace(/^(\d{3})\.(\d{3})\.(\d{3})(\d)/, "$1.$2.$3-$4")
        .slice(0, 14);
    case "cnpj":
      return v
        .replace(/\D/g, "")
        .replace(/^(\d{2})(\d)/, "$1.$2")
        .replace(/^(\d{2})\.(\d{3})(\d)/, "$1.$2.$3")
        .replace(/^(\d{2})\.(\d{3})\.(\d{3})(\d)/, "$1.$2.$3/$4")
        .replace(/^(\d{2})\.(\d{3})\.(\d{3})\/(\d{4})(\d)/, "$1.$2.$3/$4-$5")
        .slice(0, 18);
    case "telefone":
      return v
        .replace(/\D/g, "")
        .replace(/^(\d{2})(\d)/, "($1) $2")
        .replace(/(\d{5})(\d)/, "$1-$2")
        .replace(/(\d{4})(\d{4})$/, "$1-$2")
        .slice(0, 15);
    case "cep":
      return v
        .replace(/\D/g, "")
        .replace(/(\d{5})(\d)/, "$1-$2")
        .slice(0, 9);
    case "data":
      return v
        .replace(/\D/g, "")
        .replace(/(\d{2})(\d)/, "$1/$2")
        .replace(/(\d{2})(\d)/, "$1/$2")
        .slice(0, 10);
    case "numero":
      return v;
    case "moeda":
      return v
        .replace(/\D/g, "")
        .replace(/(\d)(\d{2})$/, "$1,$2")
        .replace(/(?=(\d{3})+(?!\d))/g, ".");
    default:
      return value;
  }
};

const CustomInput: React.FC<CustomInputProps> = ({
  label,
  value,
  onChange,
  placeholder,
  maxLength,
  type = "text",
  mask,
  error,
  disabled,
  readOnly,
  className,
  children,
  accept
}) => {
  
  // Estilos base compartilhados para garantir identidade visual
  const baseStyles = clsx(
    "flex w-full rounded-md border px-3 py-2 text-sm bg-white file:border-0 file:bg-transparent file:text-sm file:font-medium",
    "focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-600 focus-visible:ring-offset-2",
    "disabled:cursor-not-allowed disabled:opacity-50",
    error ? "border-red-500" : "border-gray-400",
    className
  );

  // Handler genérico para Inputs e Selects
  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
    let raw = e.target.value;
    if (mask) raw = applyMask(raw, mask);
    onChange(raw);
  };

  // Handler específico para File
  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files ? e.target.files[0] : null;
    onChange(file);
  };

  return (
    <div className="flex flex-col w-full gap-1.5">
      {/* Label estilizada */}
      {label && <FieldLabel className="text-sm font-bold text-[#851114]">{label}</FieldLabel>}

      {type === "select" ? (
        <select
          value={value as string | number | readonly string[] | undefined}
          onChange={handleChange}
          disabled={disabled}
          className={clsx(baseStyles, "appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2220%22%20height%3D%2220%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M7%2010l5%205%205-5z%22%20fill%3D%22%23666%22%2F%3E%3C%2Fsvg%3E')] bg-no-repeat bg-right pr-8")}
        >
          {children}
        </select>
      ) : type === "file" ? (
        <input
          type="file"
          disabled={disabled}
          accept={accept}
          onChange={handleFileChange}
          className={clsx(
            baseStyles,
            "text-gray-500 file:mr-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-[#14244D] file:text-white hover:file:bg-[#1e3a8a]"
          )}
        />
      ) : (
        <Input
          type={type}
          disabled={disabled}
          readOnly={readOnly}
          placeholder={placeholder}
          value={value as string | number | readonly string[] | undefined}
          onChange={handleChange}
          className={baseStyles}
        />
      )}

      {maxLength && type !== 'select' && type !== 'file' && (
        <small className="text-right text-gray-500 text-xs">
          {String(value || "").length} / {maxLength}
        </small>
      )}
    </div>
  );
};

export default CustomInput;