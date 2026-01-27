import React, { useEffect, useState, useCallback , useRef} from "react";
import { Dialog, DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { Button } from "@/components/ui/button";
import { apiFetch } from "@/utils/apiClient";
import { useTheme } from '@/context/ThemeContext';

import Spinner from "@/components/ui/Spinner"
interface ModalGridProps {
  title: string;
  apiEndpoint: string;
  columns: { key: string; label: string }[];
  onSelect: (item: any) => void;
  onClose: () => void;
  open?: boolean;
  extraParams?: Record<string, any>;
}

const ModalGrid: React.FC<ModalGridProps> = ({
  title,
  apiEndpoint,
  columns,
  onSelect,
  onClose,
  extraParams,
}) => {
  const [data, setData] = useState<any[]>([]);
  const [page, setPage] = useState(1);
  const [perPage] = useState(10);
  const [total, setTotal] = useState(0);
  const [search, setSearch] = useState("");
  const [loading, setLoading] = useState(false);
      const [searchValue, setSearchValue] = useState("");

  const { isDark } = useTheme();

  const fetchData = useCallback(
    async (pageParam = page, perPageParam = perPage, searchParam = search) => {
      setLoading(true);

      try {
        const params = new URLSearchParams({
          start: ((pageParam - 1) * perPageParam).toString(),
          length: perPageParam.toString(),
          "search[value]": searchParam,
          draw: "1",
        });

        // incluir extraParams
        if (extraParams) {
          Object.entries(extraParams).forEach(([key, value]) => {
            if (value !== undefined && value !== null) {
              params.append(key, String(value));
            }
          });
        }

        const res = await apiFetch(`${apiEndpoint}?${params}`);
        const result = await res.json();

        setData(result.data || []);
        setTotal(result.recordsTotal || 0);


      } catch (error) {
        console.error("Erro ao carregar dados:", error);
      } finally {
        setLoading(false);
      }
    },
    [apiEndpoint, extraParams]

  );


  const [appliedSearch, setAppliedSearch] = useState("");
  
  const isFirstLoad = useRef(true);

  useEffect(() => {
    if (isFirstLoad.current) {
      isFirstLoad.current = false;
      return;
    }

    if (searchValue.trim() === "") {
      setPage(1);
      setAppliedSearch("");
    }
  }, [searchValue]);



  useEffect(() => {
    fetchData(page, perPage, search);
  }, [page, perPage, fetchData]);


  const handleSearch = () => {
    setPage(1);
    fetchData(1, perPage, search);
  };




  const totalPages = Math.ceil(total / perPage);

  return (
    <Dialog open onOpenChange={onClose}>
      <DialogContent className="bg-white dark:bg-gray-800 text-gray-900 dark:text-white max-w-5xl border dark:border-gray-700">
        <DialogHeader>
          <DialogTitle>{title}</DialogTitle>
        </DialogHeader>

        {/* Barra de busca */}
        <div className="flex gap-2 mb-3 w-full justify-end">
          <input
            type="text"
            placeholder="Buscar..."
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            onKeyDown={(e) => {
              if (e.key === "Enter") {
                e.preventDefault();
                handleSearch();
              }
            }}
            className="rounded px-3 py-1 w-1/2 dark:bg-gray-800 border dark:border-gray-700"
          />

          <Button
            size="sm"
            className="bg-blue-600 hover:bg-blue-700 text-white"
            onClick={handleSearch}

          >
            Buscar
          </Button>
        </div>

        {/* Tabela */}
        <div className="overflow-x-auto max-h-[400px] border dark:border-gray-700">
          <table className="w-full border dark:border-gray-700">
            <thead>
              <tr className="bg-gray-100 dark:bg-gray-800 ">
                {columns.map((col) => (
                  <th key={col.key} className="px-2 py-1 text-left dark:text-white text-gray-900 dark:bg-gray-900">
                    {col.label}
                  </th>
                ))}
                <th className="px-2 py-1 text-center dark:text-white text-gray-900 dark:bg-gray-900">Ações</th>
              </tr>
            </thead>
            <tbody>
              {loading ? (
                <tr>
                  <td colSpan={columns.length + 1} className="justify-items-center py-4 ">
                    <Spinner />
                  </td>
                </tr>
              ) : data.length > 0 ? (
                data.map((item) => (
                  <tr key={item.ID} className="hover:bg-gray-50 dark:text-white text-gray-900 dark:bg-gray-800 border dark:border-gray-700" >
                    {columns.map((col) => (
                      <td key={col.key} className=" px-2 py-1 border dark:border-gray-700 dark:bg-gray-800">
                        {item[col.key]}
                      </td>
                    ))}
                    <td className="text-center border dark:border-gray-700 px-2 py-2 ">
                      <Button size="sm" onClick={() => onSelect(item)} className="bg-blue-600 hover:bg-gray-200 text-white hover:text-black ">
                        Selecionar
                      </Button>
                    </td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan={columns.length + 1} className="text-center py-4">
                    Nenhum registro encontrado.
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>

        {/* Paginação */}
        <div className="flex justify-between items-center mt-3">
          <span>
            Página {page} de {totalPages || 1}
          </span>
          <div className="flex gap-2">
            <Button
              size="sm"
              variant="outline"
              onClick={() => setPage((p) => Math.max(1, p - 1))}
              disabled={page === 1}
              className="hover:bg-gray-400 text-black dark:text-white"
            >
              Anterior
            </Button>
            <Button
              size="sm"
              variant="outline"
              onClick={() => setPage((p) => Math.min(totalPages, p + 1))}
              disabled={page >= totalPages}
              className="hover:bg-gray-400 text-black dark:text-white"
            >
              Próxima
            </Button>
          </div>
        </div>
      </DialogContent>
    </Dialog>
  );
};

export default ModalGrid;