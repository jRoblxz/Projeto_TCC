import React from "react";
import { FieldLabel } from "@/components/ui/field";
import { Input } from "@/components/ui/input";
import clsx from "clsx";
import { AlertTriangle } from "lucide-react";


interface CustomModalExcluirProps {
  confirmDelete: (id: number | string) => void;
  setShowModalFalse: () => void;
  itemToDelete: number | string | null | undefined;
}

const CustomModalExcluir: React.FC<CustomModalExcluirProps> = ({
  confirmDelete, 
  setShowModalFalse, 
  itemToDelete
}) => {
  return (
    <>
      <div className="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm" onClick={setShowModalFalse}>
              <div className="bg-white dark:bg-gray-800 text-gray-900 dark:text-white p-6 rounded-xl w-full max-w-md" >
                <div className="flex items-center gap-2 mb-4">
                  <AlertTriangle className="text-red-600 h-6 w-6" />
                  <h2 className="text-lg font-semibold">Confirmar exclusão</h2>
                </div>
                <p className="mb-4">
                  Deseja excluir o registro de trilha{" "}
                  <strong>{itemToDelete}</strong>?
                </p>
                <div className="flex justify-end gap-3">
                  <button
                    onClick={setShowModalFalse}
                    className="px-4 py-2 bg-gray-400 rounded-md"
                  >
                    Cancelar
                  </button>
                  <button
                    onClick={() => confirmDelete(itemToDelete!)}
                    className="px-4 py-2 bg-red-600 text-white rounded-md"
                  >
                    Confirmar
                  </button>
                </div>
              </div>
            </div>
    </>
  );
};

export default CustomModalExcluir;