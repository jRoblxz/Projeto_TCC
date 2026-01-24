import React, { useState, useEffect, FormEvent } from "react";
import Layout from "@/components/layouts/Layout"; 
import { api } from "../config/api"; 
import { useNavigate } from "react-router-dom";
import { 
  Search, Plus, Calendar, MapPin, Info, Eye, Edit, Trash, Loader2, X, ChevronLeft, ChevronRight, Filter
} from "lucide-react";
import toast from "react-hot-toast";

interface Peneira {
  id: number;
  title: string;
  date: string;
  location: string;
  status: string;
  description?: string;
  sub_divisao?: string;
}

interface PaginationMeta {
  current_page: number;
  last_page: number;
  total: number;
  per_page: number;
}

const SUB_DIVISOES = ["Todas", "Sub-7", "Sub-9", "Sub-11", "Sub-13", "Sub-15", "Sub-17", "Sub-20"];

const Peneiras: React.FC = () => {
  const navigate = useNavigate();

  // --- ESTADOS ---
  const [peneiras, setPeneiras] = useState<Peneira[]>([]);
  const [loading, setLoading] = useState<boolean>(true);
  
  // Estados de Filtro e Paginação
  const [searchTerm, setSearchTerm] = useState<string>("");
  const [activeFilter, setActiveFilter] = useState<string>("Todas"); // SubDivisão
  const [activeStatus, setActiveStatus] = useState<string>("");      // [NOVO] Status
  
  const [page, setPage] = useState<number>(1);
  const [meta, setMeta] = useState<PaginationMeta | null>(null);

  // Estados de Modais
  const [showFormModal, setShowFormModal] = useState<boolean>(false);
  const [showDeleteModal, setShowDeleteModal] = useState<boolean>(false);
  const [selectedPeneira, setSelectedPeneira] = useState<Peneira | null>(null);
  const [isEditing, setIsEditing] = useState<boolean>(false);

  // Form Data
  const initialFormData = {
    nome_evento: "",
    data_evento: "",
    local: "",
    sub_divisao: "",
    status: "AGENDADA",
    descricao: ""
  };
  const [formData, setFormData] = useState(initialFormData);

  // --- CARREGAR DADOS ---
  const loadPeneiras = async () => {
    setLoading(true);
    try {
      const params = new URLSearchParams();
      params.append('page', page.toString());
      if (searchTerm) params.append('search', searchTerm);
      if (activeFilter !== "Todas") params.append('sub_divisao', activeFilter);
      
      // [NOVO] Envia o status se estiver selecionado
      if (activeStatus && activeStatus !== "Todas") params.append('status', activeStatus);

      const response = await api.get(`/peneiras?${params.toString()}`);
      
      setPeneiras(response.data.data);
      setMeta(response.data.meta);

    } catch (error) {
      console.error("Erro ao carregar peneiras", error);
      toast.error("Erro ao carregar lista.");
    } finally {
      setLoading(false);
    }
  };

  // [ATUALIZADO] Adicionado activeStatus na dependência
  useEffect(() => {
    const delayDebounceFn = setTimeout(() => {
      loadPeneiras();
    }, 500);

    return () => clearTimeout(delayDebounceFn);
  }, [page, activeFilter, searchTerm, activeStatus]);


  // --- HANDLERS (Igual ao anterior) ---
  const handleOpenNew = () => {
    setIsEditing(false);
    setFormData(initialFormData);
    setShowFormModal(true);
  };

  const handleOpenEdit = (peneira: Peneira) => {
    setIsEditing(true);
    setSelectedPeneira(peneira);
    
    let dataFormatada = "";
    if (peneira.date) {
        const d = new Date(peneira.date);
        dataFormatada = !isNaN(d.getTime()) 
          ? d.toISOString().slice(0, 16) 
          : String(peneira.date).replace(" ", "T").slice(0, 16);
    }

    setFormData({
      nome_evento: peneira.title || "",
      data_evento: dataFormatada,
      local: peneira.location || "",
      sub_divisao: peneira.sub_divisao || "",
      status: peneira.status || "AGENDADA",
      descricao: peneira.description || ""
    });
    
    setShowFormModal(true);
  };

  const handleSave = async (e: FormEvent) => {
    e.preventDefault();
    
    if (!formData.nome_evento || !formData.data_evento || !formData.local || !formData.sub_divisao) {
      toast.error("Preencha todos os campos obrigatórios (*)");
      return;
    }

    const toastId = toast.loading("Salvando...");

    try {
      const payload = {
          ...formData,
          sub_divisao: formData.sub_divisao || "Geral"
      };

      if (isEditing && selectedPeneira) {
        await api.put(`/peneiras/${selectedPeneira.id}`, payload);
        toast.success("Atualizado!");
      } else {
        await api.post('/peneiras', payload);
        toast.success("Criado!");
      }
      setShowFormModal(false);
      loadPeneiras(); 
    } catch (error) {
      console.error(error);
      toast.error("Erro ao salvar.");
    } finally {
      toast.dismiss(toastId);
    }
  };

  const handleConfirmDelete = async () => {
    if (!selectedPeneira) return;
    const toastId = toast.loading("Excluindo...");
    try {
      await api.delete(`/peneiras/${selectedPeneira.id}`);
      toast.success("Excluído com sucesso!");
      setShowDeleteModal(false);
      loadPeneiras();
    } catch (error: any) {
      const msg = error.response?.data?.error || "Erro ao excluir.";
      toast.error(msg);
    } finally {
      toast.dismiss(toastId);
    }
  };

  const getStatusColor = (status: string) => {
    if (!status) return 'bg-gray-100 text-gray-600';
    switch (status.toUpperCase()) {
      case 'EM_ANDAMENTO': return 'bg-green-100 text-green-800 border-green-200';
      case 'AGENDADA': return 'bg-blue-100 text-blue-800 border-blue-200';
      case 'FINALIZADA': return 'bg-gray-100 text-gray-800 border-gray-200';
      case 'CANCELADA': return 'bg-red-100 text-red-800 border-red-200';
      default: return 'bg-gray-100 text-gray-600';
    }
  };

  return (
    <Layout>
      <div className="p-6 space-y-6 min-h-screen pb-20">
        
        {/* === CABEÇALHO COM BUSCA === */}
        <div className="flex flex-col md:flex-row justify-between items-center gap-4 bg-white p-4 rounded-xl shadow-sm border border-gray-100">
          <h1 className="text-2xl font-bold text-[#14244D] font-['Segoe_UI']">
            Gerenciamento de Peneiras
          </h1>
          <div className="flex items-center gap-3 w-full md:w-auto">
            <div className="relative flex-1 md:w-64">
              <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
              <input 
                type="text" 
                placeholder="Buscar (Server-side)..." 
                value={searchTerm}
                onChange={(e) => {
                   setSearchTerm(e.target.value);
                   setPage(1); 
                }}
                className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#14244D] outline-none"
              />
            </div>
            <button onClick={handleOpenNew} className="flex items-center gap-2 bg-[#14244D] hover:bg-[#1e3a8a] text-white px-4 py-2 rounded-lg font-semibold transition shadow-md whitespace-nowrap">
              <Plus className="h-5 w-5" /> <span className="hidden sm:inline">Nova</span>
            </button>
          </div>
        </div>

        {/* === ÁREA DE FILTROS (STATUS + SUBDIVISÃO) === */}
        <div className="flex flex-col md:flex-row gap-4 items-center">
            
            {/* 1. SELECT DE STATUS (NOVO) */}
            <div className="relative w-full md:w-48 shrink-0">
                <select
                    value={activeStatus}
                    onChange={(e) => {
                        setActiveStatus(e.target.value);
                        setPage(1); // Reseta paginação
                    }}
                    className="w-full appearance-none bg-white border border-gray-300 text-gray-700 py-2 pl-4 pr-8 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#14244D] cursor-pointer"
                >
                    <option value="">Status: Todos</option>
                    <option value="AGENDADA">Agendada</option>
                    <option value="EM_ANDAMENTO">Em Andamento</option>
                    <option value="FINALIZADA">Finalizada</option>
                    <option value="CANCELADA">Cancelada</option>
                </select>
                <div className="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                    <Filter className="h-4 w-4" />
                </div>
            </div>

            {/* 2. BOTÕES DE SUBDIVISÃO (JÁ EXISTENTE) */}
            <div className="flex overflow-x-auto pb-2 gap-2 scrollbar-hide w-full">
                {SUB_DIVISOES.map((sub) => (
                    <button
                        key={sub}
                        onClick={() => {
                            setActiveFilter(sub);
                            setPage(1);
                        }}
                        className={`
                            px-4 py-2 rounded-lg text-sm font-semibold transition whitespace-nowrap border
                            ${activeFilter === sub 
                                ? "bg-[#14244D] text-white border-[#14244D] shadow-md" 
                                : "bg-white text-gray-600 border-gray-200 hover:bg-gray-50"}
                        `}
                    >
                        {sub}
                    </button>
                ))}
            </div>
        </div>

        {/* === GRID DE PENEIRAS === */}
        {loading ? (
          <div className="flex justify-center h-64 items-center"><Loader2 className="h-10 w-10 text-[#14244D] animate-spin" /></div>
        ) : (
          <>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {peneiras.length > 0 ? (
                peneiras.map((peneira) => (
                  <div key={peneira.id} className="bg-white rounded-xl shadow-sm hover:shadow-lg transition border border-gray-200 flex flex-col justify-between overflow-hidden group">
                    <div className="p-5 border-b border-gray-100">
                      <div className="flex justify-between items-start mb-3">
                        <h3 className="font-bold text-lg text-gray-800 leading-tight group-hover:text-[#14244D] transition">
                          {peneira.title}
                        </h3>
                        <span className={`text-xs font-bold px-2 py-1 rounded uppercase border ${getStatusColor(peneira.status)}`}>
                          {peneira.status ? peneira.status.replace('_', ' ') : 'N/A'}
                        </span>
                      </div>
                    </div>

                    <div className="p-5 space-y-3 flex-1 text-sm text-gray-600">
                      <div className="flex items-center gap-2">
                        <Calendar className="h-4 w-4 text-[#941B1B]" />
                        <span>{peneira.date ? new Date(peneira.date).toLocaleDateString() : 'Data N/A'}</span>
                      </div>
                      <div className="flex items-center gap-2">
                        <MapPin className="h-4 w-4 text-[#941B1B]" />
                        <span className="truncate">{peneira.location}</span>
                      </div>
                      <div className="flex items-center gap-2">
                        <Filter className="h-4 w-4 text-[#941B1B]" />
                        <span className="truncate font-medium text-[#14244D]">{peneira.sub_divisao || 'Geral'}</span>
                      </div>
                      {peneira.description && (
                        <p className="text-xs text-gray-400 mt-2 line-clamp-2 italic">"{peneira.description}"</p>
                      )}
                    </div>

                    <div className="bg-gray-50 p-4 flex justify-between gap-2 border-t border-gray-100">
                       <button onClick={() => navigate(`/peneiras/${peneira.id}`)} className="flex-1 flex justify-center gap-1 bg-green-600 hover:bg-green-700 text-white py-2 rounded text-xs font-bold transition"><Eye className="h-3 w-3"/> Ver</button>
                       <button onClick={() => handleOpenEdit(peneira)} className="flex-1 flex justify-center gap-1 bg-yellow-500 hover:bg-yellow-600 text-white py-2 rounded text-xs font-bold transition"><Edit className="h-3 w-3"/> Editar</button>
                       <button onClick={() => { setSelectedPeneira(peneira); setShowDeleteModal(true); }} className="flex-1 flex justify-center gap-1 bg-red-600 hover:bg-red-700 text-white py-2 rounded text-xs font-bold transition"><Trash className="h-3 w-3"/> Excluir</button>
                    </div>
                  </div>
                ))
              ) : (
                <div className="col-span-full flex flex-col items-center justify-center py-20 text-gray-400 bg-white border border-dashed rounded-xl">
                  <Search className="h-12 w-12 mb-4 opacity-20" />
                  <p>Nenhuma peneira encontrada com estes filtros.</p>
                </div>
              )}
            </div>

            {/* === PAGINAÇÃO === */}
            {meta && meta.last_page > 1 && (
                <div className="flex justify-center items-center gap-4 mt-8">
                    <button
                        onClick={() => setPage((p) => Math.max(1, p - 1))}
                        disabled={page === 1}
                        className="p-2 rounded-lg border bg-white disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
                    >
                        <ChevronLeft className="h-5 w-5" />
                    </button>
                    
                    <span className="text-sm font-medium text-gray-600">
                        Página {meta.current_page} de {meta.last_page}
                    </span>

                    <button
                        onClick={() => setPage((p) => Math.min(meta.last_page, p + 1))}
                        disabled={page === meta.last_page}
                        className="p-2 rounded-lg border bg-white disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
                    >
                        <ChevronRight className="h-5 w-5" />
                    </button>
                </div>
            )}
          </>
        )}

        {/* MODAL FORM (Mantido igual) */}
        {showFormModal && (
          <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div className="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden animate-in fade-in zoom-in duration-200">
              <div className="bg-[#14244D] p-4 flex justify-between items-center text-white">
                <h3 className="font-bold text-lg">{isEditing ? "Editar Peneira" : "Nova Peneira"}</h3>
                <button onClick={() => setShowFormModal(false)}><X className="h-5 w-5" /></button>
              </div>
              <form onSubmit={handleSave} className="p-6 space-y-4">
                <div>
                  <label className="block text-sm font-medium mb-1">Título *</label>
                  <input type="text" required value={formData.nome_evento} onChange={(e) => setFormData({...formData, nome_evento: e.target.value})} className="w-full border p-2 rounded outline-none focus:ring-2 focus:ring-[#14244D]" />
                </div>
                <div className="grid grid-cols-2 gap-4">
                    <div>
                        <label className="block text-sm font-medium mb-1">Data *</label>
                        <input type="datetime-local" required value={formData.data_evento} onChange={(e) => setFormData({...formData, data_evento: e.target.value})} className="w-full border p-2 rounded outline-none focus:ring-2 focus:ring-[#14244D]" />
                    </div>
                    <div>
                        <label className="block text-sm font-medium mb-1">Local *</label>
                        <input type="text" required value={formData.local} onChange={(e) => setFormData({...formData, local: e.target.value})} className="w-full border p-2 rounded outline-none focus:ring-2 focus:ring-[#14244D]" />
                    </div>
                </div>
                <div className="grid grid-cols-2 gap-4">
                    <div>
                        <label className="block text-sm font-medium mb-1">Subdivisão *</label>
                        <select 
                             value={formData.sub_divisao} 
                             onChange={(e) => setFormData({...formData, sub_divisao: e.target.value})} 
                             className="w-full border p-2 rounded outline-none focus:ring-2 focus:ring-[#14244D] bg-white"
                        >
                            <option value="">Selecione...</option>
                            {SUB_DIVISOES.filter(s => s !== 'Todas').map(s => (
                                <option key={s} value={s}>{s}</option>
                            ))}
                            <option value="Profissional">Profissional</option>
                        </select>
                    </div>
                    <div>
                        <label className="block text-sm font-medium mb-1">Status</label>
                        <select value={formData.status} onChange={(e) => setFormData({...formData, status: e.target.value})} className="w-full border p-2 rounded outline-none focus:ring-2 focus:ring-[#14244D] bg-white">
                          <option value="AGENDADA">Agendada</option>
                          <option value="EM_ANDAMENTO">Em Andamento</option>
                          <option value="FINALIZADA">Finalizada</option>
                          <option value="CANCELADA">Cancelada</option>
                        </select>
                    </div>
                </div>
                <div>
                  <label className="block text-sm font-medium mb-1">Descrição</label>
                  <textarea rows={3} value={formData.descricao} onChange={(e) => setFormData({...formData, descricao: e.target.value})} className="w-full border p-2 rounded outline-none focus:ring-2 focus:ring-[#14244D] resize-none" />
                </div>
                <div className="flex justify-end gap-3 pt-4 border-t">
                  <button type="button" onClick={() => setShowFormModal(false)} className="px-4 py-2 bg-gray-100 rounded hover:bg-gray-200">Cancelar</button>
                  <button type="submit" className="px-4 py-2 bg-[#14244D] text-white rounded hover:bg-[#1e3a8a]">Salvar</button>
                </div>
              </form>
            </div>
          </div>
        )}

        {/* MODAL DELETE */}
        {showDeleteModal && selectedPeneira && (
          <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div className="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center animate-in fade-in zoom-in duration-200">
              <div className="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100 mb-4">
                 <Trash className="h-6 w-6 text-red-600" />
              </div>
              <h3 className="text-lg font-bold mb-2">Confirmar Exclusão</h3>
              <p className="text-sm text-gray-500 mb-6">Excluir <strong>"{selectedPeneira.title}"</strong> e todos os dados vinculados?</p>
              <div className="flex gap-3 justify-center">
                <button onClick={() => setShowDeleteModal(false)} className="px-4 py-2 bg-gray-100 rounded hover:bg-gray-200">Cancelar</button>
                <button onClick={handleConfirmDelete} className="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Excluir Tudo</button>
              </div>
            </div>
          </div>
        )}
      </div>
    </Layout>
  );
};

export default Peneiras;