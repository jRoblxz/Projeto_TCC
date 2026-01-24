import React, { useState, useEffect, useCallback, useRef } from "react";
import StyledDataTable from "@/components/ui/StyledDataTable";
import { Pencil, Trash, Search } from "lucide-react";
import { apiFetch } from "@/utils/apiClient";
import toast from "react-hot-toast";
import Spinner from "@/components/ui/Spinner"

import { useTheme } from '@/context/ThemeContext';

interface GridProps {
    title: string;
    apiEndpoint: string;
    columns: { key: string; label: string }[];
    // Tornamos opcional (?) para evitar erro de Tipagem
    onEdit?: (item: any) => void;
    onDelete?: (item: any) => void;
    themeDark?: boolean;
    extraParams?: Record<string, string | number>;
    disableActions?: boolean;
}

const Grid: React.FC<GridProps> = ({
    title,
    apiEndpoint,
    columns,
    onEdit,
    onDelete,
    themeDark,
    extraParams,
    disableActions = false
}) => {
    const { isDark } = useTheme();

    const [data, setData] = useState<any[]>([]);
    const [loading, setLoading] = useState(false);
    const [totalRows, setTotalRows] = useState(0);
    const [perPage, setPerPage] = useState(10);
    const [page, setPage] = useState(1);
    const [searchValue, setSearchValue] = useState("");

    const fetchData = useCallback(
        async (page = 1, perPage = 10, search = "") => {
            setLoading(true);
            try {
                const params = new URLSearchParams({
                    start: ((page - 1) * perPage).toString(),
                    length: perPage.toString(),
                    "search[value]": search,
                    draw: "1",
                });

                if (extraParams) {
                    Object.entries(extraParams).forEach(([key, value]) => {
                        params.append(key, String(value));
                    });
                }

                const res = await apiFetch(`${apiEndpoint}?${params}`);
                const result = await res.json();
                setData(result.data || []);
                setTotalRows(result.recordsFiltered || 0);
            } catch (err: any) {
                toast.error("Erro ao carregar dados: " + err.message);
            } finally {
                setLoading(false);
            }
        }, [apiEndpoint]);

    const [appliedSearch, setAppliedSearch] = useState("");
    const isFirstLoad = useRef(true);

    //
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

    // ÚNICO efeito que chama a API
    useEffect(() => {
        fetchData(page, perPage, appliedSearch);
    }, [page, perPage, appliedSearch]);

    // Buscar ao clicar
    const handleSearch = () => {
        setPage(1);
        setAppliedSearch(searchValue.trim());
    };

    //  Colunas + Ações
    const tableColumns = [
        ...columns.map((col) => ({
            name: col.label,
            selector: (row: any) => row[col.key],
            sortable: true,
            wrap: true,
        })),

        {
            name: "AÇÕES",
            cell: (row: any) => (
                <div className="flex gap-2">
                    {/* Botão EDITAR: Só renderiza se a função onEdit existir */}
                    {onEdit && (
                        <button
                            onClick={!disableActions ? () => onEdit && onEdit(row) : undefined}
                            disabled={disableActions}
                            className={`p-1 rounded-full transition-colors ${disableActions
                                ? "bg-gray-400 cursor-not-allowed"
                                : "bg-blue-600 hover:bg-blue-700 text-white"
                                }`}
                            title={disableActions ? "Ação desabilitada" : "Editar"}
                        >
                            <Pencil className="h-4 w-4" />
                        </button>
                    )}

                    {/* Botão EXCLUIR: Só renderiza se a função onDelete existir */}
                    {onDelete && (
                        <button
                            onClick={!disableActions ? () => onDelete && onDelete(row) : undefined}
                            disabled={disableActions}
                            className={`p-1 rounded-full transition-colors ${disableActions
                                ? "bg-gray-400 cursor-not-allowed"
                                : "bg-red-600 hover:bg-red-700 text-white"
                                }`}
                            title={disableActions ? "Ação desabilitada" : "Excluir"}
                        >
                            <Trash className="h-4 w-4" />
                        </button>
                    )}
                </div>
            ),
            ignoreRowClick: true,
            width: "120px",
        },

    ];

    return (
        <div className="w-full">
            <h2 className="text-xl font-semibold text-gray-800 dark:text-white mb-4">
                {title}
            </h2>

            {/* 🔍 BUSCA */}
            <div className="flex items-center justify-between gap-4 mb-4 flex-wrap">

                <div className="flex items-center gap-2 w-full sm:w-auto">
                    {/* INPUT DE PESQUISA */}
                    <div className="relative w-full sm:w-64">
                        <input
                            type="text"
                            placeholder="Pesquisar..."
                            value={searchValue}
                            onChange={(e) => !disableActions && setSearchValue(e.target.value)}
                            disabled={disableActions}
                            className="pl-10 pr-4 py-2 w-full border border-gray-300 dark:border-gray-700 rounded-lg 
                           focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800"
                        />
                        <Search className="absolute left-3 top-2.5 h-5 w-5 text-gray-400" />
                    </div>

                    {/* BOTÃO DE BUSCAR */}
                    <button
                        onClick={!disableActions ? handleSearch : undefined}
                        disabled={disableActions}
                        className={`
        flex items-center gap-2 px-4 py-2 rounded-lg transition
        ${disableActions
                                ? "bg-gray-400 cursor-not-allowed text-white"
                                : "bg-blue-600 hover:bg-blue-700 text-white font-semibold"}
    `}
                    >
                        <Search className="h-4 w-4" />
                        Buscar
                    </button>
                </div>

            </div>


            {/* 📝 GRID */}
            <StyledDataTable
                columns={tableColumns}
                data={data}
                progressPending={loading}
                progressComponent={<Spinner />}
                theme={themeDark ? "dark" : "light"}
                pagination
                paginationServer
                paginationTotalRows={totalRows}
                onChangeRowsPerPage={(newPerPage, newPage) => {
                    setPerPage(newPerPage);
                    setPage(newPage);
                }}
                onChangePage={(newPage) => setPage(newPage)}
                paginationRowsPerPageOptions={[10, 25, 50, 100]}

                striped
                responsive
                noDataComponent="Nenhum registro encontrado"
            />
        </div>
    );
};

export default Grid;
