import React from "react";
import { FieldLabel } from "@/components/ui/field";
import { Input } from "@/components/ui/input";
import clsx from "clsx";
import { AlertTriangle, CheckCircle } from "lucide-react";

interface SuccessComponentProps {
  formData: number | string | null | undefined;
}

const SuccessComponent: React.FC<SuccessComponentProps> = ({
  formData,
}) => {
  return (
    <>
      <div className="mb-6 flex flex-col items-center justify-center p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg text-green-700 dark:text-green-400 animate-in fade-in slide-in-from-top-2 duration-300">
        <CheckCircle className="h-8 w-8 mb-2" />
        <span className="text-lg font-bold">
          Registro Salvo! ID: #{formData}
        </span>
        <p className="text-sm">Clique no botão abaixo para voltar à tabela.</p>
      </div>
    </>
  );
};

export default SuccessComponent;
