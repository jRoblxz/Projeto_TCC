import React from "react";
import { Input } from "@/components/ui/input";
import { FieldLabel } from "@/components/ui/field";
import clsx from "clsx";

interface CustomInputProps {
  label?: string;
  value: string | number;
  onChange: (value: string) => void;
  placeholder?: string;
  maxLength?: number;
  type?: string;
  mask?: "cpf" | "cnpj" | "telefone" | "cep" | "data" | "numero" | "moeda";
  error?: boolean;
  disabled?: boolean;
  readOnly?: boolean;
  className?: string;
}

const applyMask = (value: string, mask?: string) => {
  let v = value.replace(/\D/g, "");

  switch (mask) {
    case "cpf":
    return v
        .replace(/\D/g, "")                             // Remove tudo que não é número
        .replace(/^(\d{3})(\d)/, "$1.$2")               // 123.4
        .replace(/^(\d{3})\.(\d{3})(\d)/, "$1.$2.$3")   // 123.456.7
        .replace(/^(\d{3})\.(\d{3})\.(\d{3})(\d)/, "$1.$2.$3-$4") // 123.456.789-1
        .slice(0, 14); 

    case "cnpj":
    return v
        .replace(/\D/g, "")                         // só número
        .replace(/^(\d{2})(\d)/, "$1.$2")           // 44.2
        .replace(/^(\d{2})\.(\d{3})(\d)/, "$1.$2.$3") // 44.282.5
        .replace(/^(\d{2})\.(\d{3})\.(\d{3})(\d)/, "$1.$2.$3/$4") // 44.282.541/0
        .replace(/^(\d{2})\.(\d{3})\.(\d{3})\/(\d{4})(\d)/, "$1.$2.$3/$4-$5") // final
        .slice(0, 18);                              // tamanho máximo

    case "telefone":
    return v
        .replace(/\D/g, "") // remove tudo que não é número
        .replace(/^(\d{2})(\d)/, "($1) $2") // (11) 9...
        .replace(/(\d{5})(\d)/, "$1-$2") // celular: 5 dígitos antes do hífen
        .replace(/(\d{4})(\d{4})$/, "$1-$2") // fixo: 4 dígitos antes do hífen
        .slice(0, 15);

    case "cep":
    return v
        .replace(/\D/g, "")                // remove tudo que não for número
        .replace(/(\d{5})(\d)/, "$1-$2")   // adiciona o hífen após 5 dígitos
        .slice(0, 9);  

    case "data":
    return v
        .replace(/\D/g, "")                 // remove tudo que não é número
        .replace(/(\d{2})(\d)/, "$1/$2")    // coloca a primeira barra
        .replace(/(\d{2})(\d)/, "$1/$2")    // coloca a segunda barra
        .slice(0, 10);  

    case "numero":
      return v;

    case "moeda":
    return v
        .replace(/\D/g, "")                 // remove tudo que não é número
        .replace(/(\d)(\d{2})$/, "$1,$2")   // coloca a vírgula antes dos 2 últimos dígitos
        .replace(/(?=(\d{3})+(?!\d))/g, "."); // adiciona os pontos dos milhares

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
}) => {
  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const raw = e.target.value;
    const masked = applyMask(raw, mask);
    onChange(masked);
  };

  return (
    <>
      {label && <FieldLabel className="text-sm font-medium">{label}</FieldLabel>}

      <div className="flex flex-col w-full">
        <Input
          type={type}
          disabled={disabled}
          readOnly={readOnly}
          placeholder={placeholder}
          value={value}
          onChange={handleChange}
          className={clsx(
            "flex flex-col w-full border border-gray-400",
            error ? "border-red-500" : "border-gray-600",
            disabled && "opacity-80 cursor-not-allowed",
            className
          )}
        />

        {maxLength && (
          <small className="text-right text-gray-500 ">
            {String(value).length} / {maxLength}
          </small>
        )}
      </div>
    </>
  );
};

export default CustomInput;
