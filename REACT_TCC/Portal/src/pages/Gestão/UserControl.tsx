import React, { useState, useEffect } from "react";
import Layout from "@/components/layouts/Layout";
import { api } from "@/config/api";
import { useNavigate } from "react-router-dom";
import DataTable, { TableColumn } from "react-data-table-component";
import { 
  Users, Search, Shield, Dumbbell, User as UserIcon, 
  UserPlus, FileDown, Edit, Trash2
} from "lucide-react";
import toast from "react-hot-toast";

// Tipagens
interface UserRecord {
  id: number;
  name: string;
  email: string;
  role: "adm" | "treinador" | "candidato";
  created_at: string;
  pessoa?: {
    nome_completo: string;
    foto_url_completa?: string;
    sub_divisao?: string;
    treinador?: { cargo: string };
    jogador?: { id: number; posicao_principal: string };
  };
}

const UserControl: React.FC = () => {
  const navigate = useNavigate();
  const [users, setUsers] = useState<UserRecord[]>([]);
  const [loading, setLoading] = useState<boolean>(true);
  
  // Estados da Paginação e Ordenação
  const [totalRows, setTotalRows] = useState(0);
  const [perPage, setPerPage] = useState(10);
  const [currentPage, setCurrentPage] = useState(1);
  const [sortField, setSortField] = useState("created_at");
  const [sortDir, setSortDir] = useState("desc");
  
  // Filtros
  const [searchTerm, setSearchTerm] = useState("");
  const [roleFilter, setRoleFilter] = useState<string>("");
  const [subFilter, setSubFilter] = useState<string>("");

  const loadUsers = async (page = currentPage, limit = perPage, field = sortField, dir = sortDir) => {
    setLoading(true);
    try {
      const params: any = { 
        page, 
        per_page: limit,
        sort_field: field,
        sort_dir: dir
      };
      if (searchTerm) params.search = searchTerm;
      if (roleFilter) params.role = roleFilter;
      if (roleFilter === "candidato" && subFilter) params.subdivisao = subFilter;

      const response = await api.get("/users", { params });
      
      setUsers(response.data.data); 
      setTotalRows(response.data.total);
      setCurrentPage(page);
    } catch (error) {
      toast.error("Erro ao carregar os usuários.");
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    const delayDebounceFn = setTimeout(() => {
      loadUsers(1, perPage, sortField, sortDir);
    }, 500);
    return () => clearTimeout(delayDebounceFn);
  }, [searchTerm, roleFilter, subFilter]);

  const handlePageChange = (page: number) => {
    loadUsers(page, perPage, sortField, sortDir);
  };

  const handlePerRowsChange = async (newPerPage: number, page: number) => {
    setPerPage(newPerPage);
    loadUsers(page, newPerPage, sortField, sortDir);
  };

  // Função disparada ao clicar no topo da tabela para ordenar
  const handleSort = async (column: any, sortDirection: string) => {
    setSortField(column.sortField);
    setSortDir(sortDirection);
    loadUsers(currentPage, perPage, column.sortField, sortDirection);
  };

  const handleDelete = async (id: number, name: string) => {
    if (window.confirm(`Tem certeza que deseja excluir o acesso de ${name}?`)) {
      try {
        await api.delete(`/users/${id}`);
        toast.success("Usuário excluído com sucesso!");
        loadUsers(currentPage, perPage, sortField, sortDir);
      } catch (error) {
        toast.error("Erro ao excluir usuário.");
      }
    }
  };

  // --- LÓGICA INTELIGENTE DE EDIÇÃO ---
  const handleEdit = (user: UserRecord) => {
    // Se for jogador, manda para a tela de edição de jogadores (que você já tem no sistema)
    if (user.role === "candidato" && user.pessoa?.jogador) {
      navigate(`/jogadores/${user.pessoa.jogador.id}/edit`); // Ajuste esta rota se a sua for diferente
    } else {
      // Se for Admin ou Treinador, manda para uma futura tela de edição de equipe
      navigate(`/usuarios/${user.id}/edit`);
    }
  };

  const handleExportCSV = () => {
    toast.success("Gerando relatório CSV...");
  };

  const renderRoleBadge = (role: string) => {
    switch (role) {
      case "adm":
        return <span className="flex items-center gap-1 w-fit px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-bold border border-blue-200"><Shield size={12}/> Admin</span>;
      case "treinador":
        return <span className="flex items-center gap-1 w-fit px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-bold border border-purple-200"><Dumbbell size={12}/> Treinador</span>;
      case "candidato":
        return <span className="flex items-center gap-1 w-fit px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-bold border border-green-200"><UserIcon size={12}/> Jogador</span>;
      default:
        return <span className="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-bold border border-gray-200">{role}</span>;
    }
  };

  // --- DEFINIÇÃO DAS COLUNAS (DATA TABLE) ---
  const columns: TableColumn<UserRecord>[] = [
    {
      name: "Usuário",
      sortable: true,
      sortField: "name",
      minWidth: "250px",
      cell: (row) => (
        <div className="flex items-center gap-3 py-2">
          <div className="w-10 h-10 rounded-full border-2 border-gray-200 overflow-hidden flex-shrink-0 bg-gray-100">
            {row.pessoa?.foto_url_completa ? (
              <img src={row.pessoa.foto_url_completa} alt={row.name} className="w-full h-full object-cover" />
            ) : (
              <div className="w-full h-full flex items-center justify-center text-gray-400 font-bold bg-[#14244D]/5">
                {row.name.charAt(0).toUpperCase()}
              </div>
            )}
          </div>
          <div>
            <p className="font-bold text-gray-800 dark:text-white text-sm">{row.pessoa?.nome_completo || row.name}</p>
            {/* A data que ficava aqui foi removida para a sua própria coluna! */}
          </div>
        </div>
      ),
    },
    {
      name: "Contato",
      sortable: true,
      sortField: "email",
      minWidth: "200px",
      cell: (row) => <span className="text-sm text-gray-600 dark:text-gray-400 font-medium">{row.email}</span>,
    },
    {
      name: "Nível de Acesso",
      sortable: true,
      sortField: "role",
      minWidth: "140px",
      cell: (row) => renderRoleBadge(row.role),
    },
    {
      name: "Detalhes",
      minWidth: "160px",
      cell: (row) => (
        <div className="flex flex-col gap-1 py-2">
          {row.role === "candidato" && row.pessoa?.sub_divisao && (
            <span className="text-xs font-bold text-[#8B0000]">{row.pessoa.sub_divisao}</span>
          )}
          {row.role === "candidato" && row.pessoa?.jogador?.posicao_principal && (
            <span className="text-xs text-gray-500">Pos. {row.pessoa.jogador.posicao_principal}</span>
          )}
          {row.role === "treinador" && row.pessoa?.treinador?.cargo && (
            <span className="text-xs font-medium text-gray-600 dark:text-gray-400">{row.pessoa.treinador.cargo}</span>
          )}
          {row.role === "adm" && <span className="text-xs text-gray-400">Acesso Total</span>}
        </div>
      ),
    },
    // ---> NOVA COLUNA DE DATA DE CADASTRO <---
    {
      name: "Data de Cadastro",
      sortable: true,
      sortField: "created_at",
      minWidth: "150px",
      cell: (row) => (
        <span className="text-sm text-gray-600 dark:text-gray-400">
          {new Date(row.created_at).toLocaleDateString('pt-BR')}
        </span>
      ),
    },
    {
      name: "Ações",
      right: true,
      minWidth: "120px",
      cell: (row) => (
        <div className="flex items-center gap-2">
          <button 
            onClick={() => handleEdit(row)} // <-- Usa a nova função inteligente
            className="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition"
            title="Editar"
          >
            <Edit size={18} />
          </button>
          <button 
            onClick={() => handleDelete(row.id, row.name)} // <-- Chama o backend para deletar
            className="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition"
            title="Excluir"
          >
            <Trash2 size={18} />
          </button>
        </div>
      ),
    },
  ];

  const customStyles = {
    headRow: {
      style: {
        backgroundColor: '#f9fafb',
        borderBottomColor: '#e5e7eb',
        fontWeight: 'bold',
        color: '#6b7280',
        textTransform: 'uppercase' as any,
        fontSize: '0.75rem',
      },
    },
    rows: {
      style: { '&:hover': { backgroundColor: '#f3f4f6' } },
    },
  };

  // Definição dos Botões de Cargo
  const roles = [
    { value: "", label: "Todos" },
    { value: "adm", label: "Admins" },
    { value: "treinador", label: "Treinadores" },
    { value: "candidato", label: "Jogadores" }
  ];

  return (
    <Layout>
      <div className="max-w-[1200px] mx-auto p-4 sm:p-6 lg:p-8 space-y-6">
        
        {/* Cabeçalho */}
        <div className="flex flex-col md:flex-row justify-between items-center bg-white dark:bg-gray-900 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 gap-4">
          <div>
            <h1 className="text-2xl font-bold text-[#14244D] dark:text-white flex items-center gap-2">
              <Users className="text-[#8B0000]" /> Gestão de Usuários
            </h1>
            <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
              Controle de acessos, edição de membros e base de atletas.
            </p>
          </div>
          <div className="flex gap-3 w-full md:w-auto">
            <button 
              onClick={handleExportCSV}
              className="flex-1 md:flex-none flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition border border-gray-200"
            >
              <FileDown size={18} /> Exportar
            </button>
            <button 
              onClick={() => navigate('/cadastro-admin')}
              className="flex-1 md:flex-none flex items-center justify-center gap-2 px-4 py-2.5 bg-[#14244D] text-white font-bold rounded-xl hover:bg-[#1e3a8a] transition shadow-md"
            >
              <UserPlus size={18} /> Novo Usuário
            </button>
          </div>
        </div>

        {/* Barra de Filtros Reformulada */}
        <div className="bg-white dark:bg-gray-900 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 flex flex-col lg:flex-row gap-5 items-center justify-between">
          
          {/* Botões de Cargos */}
          <div className="flex gap-1 bg-gray-100 dark:bg-gray-800 p-1.5 rounded-xl w-full lg:w-auto overflow-x-auto">
            {roles.map(r => (
              <button
                key={r.value}
                onClick={() => {
                  setRoleFilter(r.value);
                  setSubFilter(""); // Limpa a subdivisão ao trocar
                }}
                className={`flex-1 lg:flex-none px-4 py-2 rounded-lg text-sm font-bold transition-all whitespace-nowrap ${
                  roleFilter === r.value 
                    ? "bg-white dark:bg-gray-700 shadow-sm text-[#14244D] dark:text-white" 
                    : "text-gray-500 hover:text-gray-700 dark:hover:text-gray-300"
                }`}
              >
                {r.label}
              </button>
            ))}
          </div>

          <div className="flex flex-col sm:flex-row gap-4 w-full lg:w-auto flex-1 justify-end">
            {/* Filtro de Subdivisão (Só aparece em Jogadores) */}
            {roleFilter === "candidato" && (
              <div className="flex items-center bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-3 animate-in fade-in zoom-in">
                <select 
                  value={subFilter} 
                  onChange={(e) => setSubFilter(e.target.value)}
                  className="bg-transparent border-none outline-none text-sm font-bold text-[#8B0000] cursor-pointer py-2.5 w-full"
                >
                  <option value="">Todas Categorias</option>
                  {["Sub-7", "Sub-9", "Sub-11", "Sub-13", "Sub-15", "Sub-17", "Sub-20"].map((sub) => (
                    <option key={sub} value={sub}>{sub}</option>
                  ))}
                </select>
              </div>
            )}

            {/* Pesquisa */}
            <div className="relative min-w-[250px]">
              <Search className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5" />
              <input
                type="text"
                placeholder="Buscar usuário..."
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                className="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-[#14244D] outline-none transition text-sm"
              />
            </div>
          </div>
        </div>

        {/* Tabela Profissional (Data-Table) */}
        <div className="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
          <DataTable
            columns={columns}
            data={users}
            progressPending={loading}
            sortServer // <-- ATIVA A ORDENAÇÃO NO SERVIDOR
            onSort={handleSort} // <-- DISPARA A FUNÇÃO AO CLICAR NA COLUNA
            progressComponent={
              <div className="p-10 flex flex-col items-center justify-center">
                <div className="w-10 h-10 border-4 border-t-[#14244D] border-gray-200 rounded-full animate-spin mb-4"></div>
                <p className="text-gray-500 font-medium">Carregando usuários...</p>
              </div>
            }
            pagination
            paginationServer
            paginationTotalRows={totalRows}
            onChangeRowsPerPage={handlePerRowsChange}
            onChangePage={handlePageChange}
            paginationComponentOptions={{
              rowsPerPageText: 'Linhas por página:',
              rangeSeparatorText: 'de',
              selectAllRowsItem: true,
              selectAllRowsItemText: 'Todos',
            }}
            customStyles={customStyles}
            noDataComponent={
              <div className="p-10 text-center text-gray-500">Nenhum usuário encontrado para estes filtros.</div>
            }
          />
        </div>

      </div>
    </Layout>
  );
};

export default UserControl;